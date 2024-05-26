# Background Tasks and Notifications

Various actions in the portals like batch updating, batch downloading etc. result in long-running processes. The portal
engine executes these long-running processes asynchronously in the background and notifies the user about progress
and when the tasks are finished. 

<div class="image-as-lightbox"></div>

![Background Task Running](../../img/user_docs/background-task-running.png)

Depending on the task type, a call to action button is available when task is finished. 

<div class="image-as-lightbox"></div>

![Background Task Finished](../../img/user_docs/background-task-finished.png)

It is also possible to cancel a long-running task by clicking the x in the notification. 

<div class="image-as-lightbox"></div>
 
![Background Task Cancel](../../img/user_docs/background-task-cancel.png)