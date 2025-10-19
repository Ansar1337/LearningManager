# earningManager

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
5. The frontend application will work over the remote backend server at `https://rtlm.tableer.com`.

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

1. [ ] Dialogs
    1. [x] Login
    2. [x] Registration
    3. [ ] Feedback form
2. [ ] Header menus/buttons
    1. [x] Login
    2. [ ] Notifications
    3. [ ] User's menu
    4. [x] Logout
3. [x] Homepage
4. [ ] Courses' description page
5. [ ] User's cabinet mainpage
    1. [ ] "My learning" page
    2. [ ] My learning -> Course details page
    3. [ ] My learning -> Course details -> Module page
    4. [ ] My learning -> Course details -> Module page -> Lections page
    5. [ ] My learning -> Course details -> Module page -> Homework page
    6. [ ] My learning -> Course details -> Module page -> Test page
    7. [ ] Profile settings -> Personal data page
    8. [ ] Profile settings -> Credentials page
    9. [ ] Profile settings -> Email page
    10. [ ] Performance Page

### API+StateManagement:

1. [x] Session Management
    1. [x] Check account state
    2. [x] Register account
        1. [x] Login reservation
        2. [x] Register login in system
    3. [x] Log in
    4. [x] Log out
2. [ ] Courses management
    1. [x] Fetch available courses data
        1. [x] Metadata only (name, icon, short description)
        2. [x] Full description
    2. [ ] Fetch User's courses
        1. [ ] Unread messages
        2. [x] Metadata only
        3. [ ] Modules by courseId
            1. [x] Metadata (timings, performance etc)
            2. [X] Materials + status
                1. [X] Article groups
                    1. [X] Articles + status
            3. [ ] Homework + status
                1. [ ] Task
                2. [ ] File upload
                3. [ ] Status
                4. [ ] Comments
            4. [ ] Tests + status
                1. [ ] Get questions
                2. [ ] Check Questions
    3. [ ] Profile data
        1. [ ] Feed data
        2. [ ] Update data