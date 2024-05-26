<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\PortalConfig as PortalConfigEditablesEnum;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\PortalConfig;
use Pimcore\Cache\Symfony\CacheClearer;
use Pimcore\Tool;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Translation\TranslatorInterface;

class FrontendBuildService
{
    const APP_FRONTEND_RELATIVE_ROOT = '/var/portal-engine';
    const APP_FRONTEND_ROOT = PIMCORE_WEB_ROOT . self::APP_FRONTEND_RELATIVE_ROOT;
    const APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS = PIMCORE_WEB_ROOT . '/portal-engine';
    const APP_FRONTEND_PORTAL_CONFIGS_ROOT = self::APP_FRONTEND_ROOT . '/portal-configs';
    const APP_FRONTEND_JSON_ROOT = self::APP_FRONTEND_PORTAL_CONFIGS_ROOT . '/json';
    const APP_FRONTEND_PORTALS_JSON = self::APP_FRONTEND_JSON_ROOT . '/portals.json';
    const APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS_JSON = self::APP_FRONTEND_JSON_ROOT . '/customized-frontend-builds.json';

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var DefaultValuesService $defaultValuesService
     */
    protected $defaultValuesService;

    /**
     * @var CacheClearer
     */
    protected $cacheClearer;

    /**
     * @var array
     */
    protected $customizedFrontendBuilds;

    /**
     * FrontendBuildService constructor.
     *
     * @param PortalConfigService $portalConfigService
     */
    public function __construct(
        PortalConfigService $portalConfigService,
        KernelInterface $kernel,
        TranslatorInterface $translator,
        DefaultValuesService $defaultValuesService,
        CacheClearer $cacheClearer,
        array $customizedFrontendBuilds = []
    ) {
        $this->portalConfigService = $portalConfigService;
        $this->kernel = $kernel;
        $this->translator = $translator;
        $this->defaultValuesService = $defaultValuesService;
        $this->cacheClearer = $cacheClearer;
        $this->customizedFrontendBuilds = $customizedFrontendBuilds;
    }

    public function publishCustomizedBuild(PortalConfig $portalConfig)
    {
        $this->updatePortalsJson();

        $bundleBasePath = realpath(__DIR__.'/../../..');

        $buildSubPath = '/build/portal_'.$portalConfig->getPortalId();
        $relativeBuildPath = self::APP_FRONTEND_RELATIVE_ROOT . $buildSubPath;
        $buildPath = self::APP_FRONTEND_ROOT . $buildSubPath;
        $this->recursiveDelete($buildPath);
        mkdir($buildPath, 0777, true);
        $this->recurseCopy($bundleBasePath.'/src/Resources/public/build/customized', $buildPath);

        $entrypointsJsonFile = $buildPath . '/entrypoints.json';
        $entrypointsJson = json_decode(file_get_contents($entrypointsJsonFile), true);

        if ($handle = opendir($buildPath)) {
            while (false !== ($file = readdir($handle))) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = pathinfo($file, PATHINFO_FILENAME);

                if ($extension == 'css') {
                    $newFilename = explode('.', $filename);
                    $newFilename[sizeof($newFilename) - 1] = uniqid();
                    $newFilename = implode($newFilename);

                    $fileFullPath = $buildPath.'/'.$filename.'.'.$extension;
                    $newFileFullPath = $buildPath.'/'.$newFilename.'.'.$extension;
                    $content = file_get_contents($fileFullPath);

                    foreach (PortalConfigEditablesEnum::REMOVE_FROM_CUSTOMIZED_BUILD as $fieldName => $remove) {
                        if (!$portalConfig->getElementData($fieldName)) {
                            $content = str_replace($remove, '', $content);
                        }
                    }

                    foreach (PortalConfigEditablesEnum::COLOR_FIELD_PLACEHOLDERS as $fieldName => $placeholder) {
                        $color = $this->resolveColor($portalConfig, $fieldName);
                        $content = str_replace($placeholder, $color, $content);
                    }

                    foreach (PortalConfigEditablesEnum::LUMINANCE_REPLACE as $placeholder => $settings) {
                        $color = $this->resolveColor($portalConfig, $settings['baseColor']);
                        $content = str_replace($placeholder, $this->colorLuminance($color, $settings['luminance']), $content);
                    }

                    foreach (PortalConfigEditablesEnum::COLOR_YIQ_REPLACE as $placeholder => $settings) {
                        $baseColor = $this->resolveColor($portalConfig, $settings['baseColor']);
                        $colorYiq1 = $this->resolveColor($portalConfig, $settings['colorYIQ'][0]);
                        $colorYiq2 = $this->resolveColor($portalConfig, $settings['colorYIQ'][1]);

                        $colorYiq = $this->colorYIQ($baseColor, $colorYiq1, $colorYiq2);
                        $rgba = $settings['rgba'] ?? null;
                        $luminance = $settings['luminance'] ?? null;

                        if ($rgba) {
                            $colorYiq = $this->hex2rgba($colorYiq, $rgba);
                        }

                        if ($luminance) {
                            $colorYiq = $this->colorLuminance($colorYiq, $luminance);
                        }

                        $content = str_replace($placeholder, $colorYiq, $content);
                    }

                    file_put_contents($newFileFullPath, $content);
                    unlink($fileFullPath);

                    $oldEntry = '/bundles/pimcoreportalengine/build/customized/'.$filename.'.'.$extension;
                    $newEntry = $relativeBuildPath.'/'.$newFilename.'.'.$extension;

                    foreach ($entrypointsJson['entrypoints'] as &$entryPoint) {
                        foreach ($entryPoint['css'] ?? [] as $i => &$cssEntry) {
                            if ($cssEntry === $oldEntry) {
                                $entryPoint['css'][$i] = $newEntry;
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }

        file_put_contents($entrypointsJsonFile, json_encode($entrypointsJson));
    }

    protected function resolveColor(PortalConfig $portalConfig, string $colorSetting)
    {
        if (substr($colorSetting, 0, 1) === '#') {
            return $colorSetting;
        }

        return $portalConfig->getElementData($colorSetting) ?: null;
    }

    public function updatePortalsJson(bool $clearCache = false, bool $force = false)
    {
        $currentPortals = [];
        if (file_exists(self::APP_FRONTEND_PORTALS_JSON)) {
            $currentPortals = json_decode(file_get_contents(self::APP_FRONTEND_PORTALS_JSON), true);
        }

        $newPortals = [];
        foreach ($this->portalConfigService->getAllPortalConfigs() as $portalConfig) {
            $newPortals[] = [
                'id' => $portalConfig->getPortalId(),
                'customizedFrontendBuild' => $portalConfig->getCustomizedFrontendBuild(),
            ];
        }

        if ($force || $currentPortals != $newPortals) {
            $this->createDirIfNotExists(self::APP_FRONTEND_JSON_ROOT);
            file_put_contents(self::APP_FRONTEND_PORTALS_JSON, json_encode($newPortals));

            if ($clearCache) {
                foreach (Tool::getCachedSymfonyEnvironments() as $environment) {
                    $this->cacheClearer->clear($environment, [
                        'no-warmup' => true
                    ]);
                }
            }
        }
    }

    public function updateCustomizedFrontendBuildsJson()
    {
        $customizedFrontendBuilds = $this->getCustomizedFrontendBuilds();
        $this->createDirIfNotExists(self::APP_FRONTEND_JSON_ROOT);
        file_put_contents(self::APP_FRONTEND_CUSTOMIZED_FRONTEND_BUILDS_JSON, json_encode($customizedFrontendBuilds));
    }

    public function writePackageJson(OutputInterface $output, string $npmExecutable, string $npxExecutable, bool $coreBundleDevelopment = false)
    {
        $bundleBasePath = realpath(__DIR__ . '/../../..');

        $portalEngineJson = json_decode(file_get_contents($bundleBasePath . '/package.json'), true);

        $targetDir = PIMCORE_PROJECT_ROOT;
        $targetPackageJsonLocation = $targetDir .'/package.json';

        $packageJsonNewlyCreated = false;
        if (!file_exists($targetDir . '/package.json')) {
            $cmd = [
                $npmExecutable,
                'init',
                '--yes'
            ];
            $output->writeln('EXECUTE: ' . implode(' ', $cmd) . ' in ' . $targetDir);

            $process = new Process($cmd, $targetDir);
            $process->mustRun();

            $packageJsonNewlyCreated = true;
        }

        $cmd = [
            $npmExecutable,
            'install',
            'add-dependencies'
        ];

        $output->writeln('EXECUTE: ' . implode(' ', $cmd) . ' in ' . $targetDir);

        $process = new Process($cmd, $targetDir);
        $process->mustRun();

        foreach ($portalEngineJson['dependencies'] as $dependency => $version) {
            $cmd = [
                $npxExecutable,
                'add-dependencies',
                $targetPackageJsonLocation,
                $dependency . '@' . $version
            ];
            $output->writeln('EXECUTE: ' . implode(' ', $cmd) . ' in ' . $targetDir);

            $process = new Process($cmd, $targetDir);
            $process->mustRun();
        }
        $targetPackageJson = json_decode(file_get_contents($targetPackageJsonLocation), true);

        $output->writeln('add portal-engine dependency: ' . $bundleBasePath);
        $targetPackageJson['dependencies']['portal-engine'] = 'file:' . $bundleBasePath;

        if ($packageJsonNewlyCreated) {
            $targetPackageJson['name'] = 'portal-engine-project';
            $targetPackageJson['scripts'] = [];
        }

        $scripts = $targetPackageJson['scripts'];
        $webpackConfig = str_replace(PIMCORE_PROJECT_ROOT . '/', './', $bundleBasePath . '/webpack.config.js');

        foreach ($this->getCustomizedFrontendBuilds() as $customizedFrontendBuild) {
            $scripts = array_merge($scripts, [
                'dev_'.$customizedFrontendBuild => sprintf(
                    'npx encore dev --config %s --config-name portalEngineApp_%s --watch',
                    $webpackConfig,
                    $customizedFrontendBuild
                ),
                'build_'.$customizedFrontendBuild => sprintf(
                    'NODE_ENV=production npx encore production --config %s --config-name portalEngineApp_%s',
                    $webpackConfig,
                    $customizedFrontendBuild
                ),
            ]);
        }

        if ($coreBundleDevelopment) {
            $scripts = array_merge($scripts,
                [
                    'dev' => sprintf(
                        'npx encore dev --config %s --config-name portalEngineBundle --watch',
                        $webpackConfig
                    ),
                    'build' => sprintf(
                        'NODE_ENV=production npx encore production --config %s --config-name portalEngineBundle & NODE_ENV=production npx encore production --config %s --config-name portalEngineCustomized',
                        $webpackConfig,
                        $webpackConfig
                    ),
                    'build_portalengine_customized' => sprintf(
                        'NODE_ENV=production npx encore production --config %s --config-name portalEngineCustomized',
                        $webpackConfig
                    ),
                ]);
        }

        $output->writeln('update scripts section ');
        $targetPackageJson['scripts'] = $scripts;

        $output->writeln('add portal-engine dependency: ' . $bundleBasePath);

        file_put_contents($targetPackageJsonLocation, json_encode($targetPackageJson, JSON_PRETTY_PRINT));
    }

    /**
     * @return string[]
     */
    public function getCustomizedFrontendBuilds(): array
    {
        return $this->customizedFrontendBuilds;
    }

    /**
     * @param PortalConfig $portalConfig
     *
     * @return array error messages
     */
    public function validatePortalConfigCssVariables(PortalConfig $portalConfig): array
    {
        $this->defaultValuesService->setPortalPageDefaultConfig($portalConfig->getDocument());

        $errors = [];
        foreach (array_keys(PortalConfigEditablesEnum::COLOR_FIELD_PLACEHOLDERS) as $colorField) {
            $colorValue = $portalConfig->getElementData($colorField);
            if (!preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $colorValue)) {
                $errors[] = $this->translator->trans('portal-engine.invalid-hex-in-color-field', [], 'admin');
            }
        }

        return $errors;
    }

    protected function createDirIfNotExists($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.'/'.$file)) {
                    $this->recurseCopy($src.'/'.$file, $dst.'/'.$file);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }

    protected function recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        } elseif (is_dir($str)) {
            $scan = glob(rtrim($str, '/').'/*');
            foreach ($scan as $index => $path) {
                $this->recursiveDelete($path);
            }

            return @rmdir($str);
        }
    }

    protected function colorLuminance($hexcolor, $percent)
    {
        if (strlen($hexcolor) < 6) {
            $hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
        }
        $hexcolor = array_map('hexdec', str_split(str_pad(str_replace('#', '', $hexcolor), 6, '0'), 2));

        foreach ($hexcolor as $i => $color) {
            $from = $percent < 0 ? 0 : $color;
            $to = $percent < 0 ? $color : 255;
            $pvalue = ceil(($to - $from) * $percent);
            $hexcolor[$i] = str_pad(dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
        }

        return '#' . implode($hexcolor);
    }

    /**
     * returns either $dark or $light color depending on the contrast
     */
    protected function colorYIQ($baseColor, $dark, $light)
    {
        $baseColor = str_replace('#', '', $baseColor);
        $r = hexdec(substr($baseColor, 0, 2));
        $g = hexdec(substr($baseColor, 2, 2));
        $b = hexdec(substr($baseColor, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? $dark : $light;
    }

    protected function hex2rgba($color, $opacity = false)
    {
        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color)) {
            return $default;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
        } elseif (strlen($color) == 3) {
            $hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba('.implode(',', $rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(',', $rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }
}
