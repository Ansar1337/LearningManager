# LearningManager

## Docs

1. [Backend API](./src/backend/help.md);
2. [State managers](./src/stores/docs);

----

## Application

### How to run the Frontend

1. Clone the repo.
2. Prepare:
    ```
    npm install
    ```
3. Start in dev mode:
   ```
   npm run dev
   ```
4. Build:
   ```
   npm run build
   ```
5. The frontend application will work over the backend server.

   API reference can be found [here](./src/backend/help.md).
   API test dashboard can be found at `/api` page of the project.

### How to run the Backend

This repo also includes a backend part, so it's possible to launch it locally:

1. Install `php8.3`, `php8.3-memcache` extension, and `memcached` server, following the instructions appropriate for
   your Linux distribution.
2. Run the server:
   ```
   /usr/bin/php -S domain:PORT -t /full/path/to/backend/directory
   ```
   For example, to run it at `localhost` on port `9999`;    
   Assuming our `backend` directory is located at /home/user/RuntimeLearningManager/src/backend:
   ```
   /usr/bin/php -S localhost:9999 -t /home/user/RuntimeLearningManager/src/backend
   ```
3. Check if it works: [https://localhost:9999](https://localhost:9999)
   ```
      I'm ready!
   ```
4. To use the local backend server, you have to specify the environment variable `VITE_API_SERVER_URL` when launching
   the frontend server in dev mode:
   ```
   VITE_API_SERVER_URL=http://localhost:9999 npm run dev
   ```
   Now, your frontend is linked to your local backend only.

 ----

## Development checklist:

### Pages/UI:

1. [x] Dialogs
    1. [x] Login
    2. [x] Registration
    3. [x] Feedback form
2. [ ] Header menus/buttons
    1. [x] Login
    2. [ ] Notifications
    3. [x] User's menu
    4. [x] Logout
3. [x] Homepage
4. [ ] Courses' description page
5. [ ] User's cabinet mainpage
    1. [x] "My learning" page
    2. [ ] My learning -> Course details page
    3. [ ] My learning -> Course details -> Module page
    4. [ ] My learning -> Course details -> Module page -> Lections page
    5. [ ] My learning -> Course details -> Module page -> Homework page
    6. [ ] My learning -> Course details -> Module page -> Test page
    7. [x] Profile settings -> Personal data page
    8. [x] Profile settings -> Credentials page
    9. [x] Profile settings -> Email page
    10. [ ] Performance Page

### API+StateManagement [DONE]:

1. [x] Session Management
    1. [x] Check account state
    2. [x] Register account
        1. [x] Login reservation
        2. [x] Register login in system
    3. [x] Log in
    4. [x] Log out
2. [x] Courses management
    1. [x] Fetch available courses data
        1. [x] Metadata only (name, icon, short description)
        2. [x] Full description
    2. [x] Fetch User's courses
        1. [x] Unread messages
        2. [x] Metadata only
        3. [x] Modules by courseId
            1. [x] Metadata (timings, performance etc)
            2. [X] Materials + status
                1. [X] Article groups
                    1. [X] Articles + status
            3. [x] Homework + status
                1. [x] Task
                2. [x] File upload
                3. [x] Status
                4. [x] Comments
            4. [x] Tests + status
                1. [x] Get questions
                2. [x] Track questions
                3. [x] Check Questions
    3. [x] Profile data
        1. [x] Feed data
        2. [x] Update data
    