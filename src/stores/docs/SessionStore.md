### Документация к `SessionStore`

`useSessionStore` - это хранилище состояний для управления сессиями пользователя в приложении Vue.js с использованием
библиотеки `Pinia`.

#### Структура состояния

`state`: Объект `ref`, хранящий состояние сессии пользователя, включает следующие свойства:

- `userId`: ID пользователя (может быть `null`).
- `userName`: Имя пользователя (может быть `null`).
- `loggedIn`: Статус входа в систему (может быть `null`).
- `role`: Роль пользователя (может быть `null`).

#### Методы

##### `checkSessionState()`

Асинхронный метод для проверки состояния текущей сессии. Вызывает функцию `doRequest` с аргументами `"sessionManager"` и
`"getSession"`. Если ответ успешен, обновляет состояние сессии данными из ответа.
----

##### `tryToLogIn(login, password)`

Асинхронный метод для попытки входа в систему. Принимает параметры `login` и `password`. Вызывает `doRequest` с
аргументами `"sessionManager"`, `"tryToLogIn"` и объектом с данными логина и пароля. При успешном ответе
вызывает `checkSessionState` для обновления состояния сессии. Возвращает ответ от `doRequest`.

----

##### `tryToLogOut()`

Асинхронный метод для выхода из системы. Вызывает `doRequest` с аргументами `"sessionManager"` и `"tryToLogOut"`. При
успешном ответе вызывает `checkSessionState` для обновления состояния сессии. Возвращает ответ от `doRequest`.

----

##### `reserveLogin(login)`

Асинхронный метод для резервирования логина. Принимает параметр `login`. Вызывает `doRequest` с
аргументами `"sessionManager"`, `"reserveLogin"` и объектом с данным логином. Возвращает ответ от `doRequest`.

----

##### `registerLogin(login, password)`

Асинхронный метод для регистрации нового логина. Принимает параметры `login` и `password`. Вызывает `doRequest` с
аргументами `"sessionManager"`, `"registerLogin"` и объектом с данными логина и пароля. Возвращает ответ от `doRequest`.

----

##### Использование

Для использования `useSessionStore` импортируйте его в ваш компонент Vue и вызывайте нужные методы для управления
сессией пользователя.

Пример:

```javascript
import {useSessionStore} from '@/stores/session';

const sessionStore = useSessionStore();

// Проверка состояния сессии
await sessionStore.checkSessionState();

// Вход в систему
await sessionStore.tryToLogIn('user_login', 'user_password');

// Выход из системы
await sessionStore.tryToLogOut();
```

Обратите внимание, что все методы асинхронные и требуют использования `await` для корректной работы.  
Документацию по ответам и ошибкам, возвращаемым doRequest, можно найти [здесь](../../backend/help.md).