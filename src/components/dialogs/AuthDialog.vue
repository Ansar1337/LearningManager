<script setup>
import {reactive, watch} from "vue";
import {useUserStore} from "@/stores/UserStore.js";

const userStore = useUserStore();
const isOpen = defineModel("isOpen", {default: false});

const initial = {
  step: 1,
  login: "",
  password: "",
  passwordRepeat: "",
  loginError: "",
  passwordError: "",
  commonError: "",
  checkLoginError: "",
  passwordMatchError: "",
  registerError: "",
  isLoading: false
};

const data = reactive({
  ...initial
});

watch(isOpen, () => Object.assign(data, initial));

function tryLogin() {
  data.isLoading = true;
  data.loginError = data.passwordError = data.commonError = "";

  userStore.sessionTools.tryToLogIn(data.login, data.password).then(result => {
    if (result.status === "success") {
      isOpen.value = false;
      return;
    }

    if (result.data === "unknown user")
      data.loginError = "Пользователь не найден";
    else if (result.data === "corrupted data")
      data.commonError = "Неполные или некорректные данные";
    else if (result.data === "already logged in")
      data.commonError = "Попытка входа после входа в систему";
    else {
      data.commonError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
      console.log("error", result.data);
    }
  }).catch(error => {
    data.commonError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
    console.log("error", error);
  }).finally(() => {
    data.isLoading = false;
  });
}

function checkLogin() {
  data.isLoading = true;
  data.checkLoginError = "";

  userStore.sessionTools.reserveLogin(data.login).then(result => {
    if (result.status === "success") {
      data.step = 3;
      return;
    }

    if (result.data === "login occupied")
      data.checkLoginError = "Логин уже занят";
    else if (result.data === "empty login")
      data.checkLoginError = "Передан пустой логин";
    else if (result.data === "already logged in")
      data.checkLoginError = "Попытка резервирования после входа в систему";
    else {
      data.checkLoginError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
      console.log("error", result.data);
    }
  }).catch(error => {
    data.checkLoginError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
    console.log("error", error);
  }).finally(() => {
    data.isLoading = false;
  });
}

function register() {
  data.passwordMatchError = data.registerError = "";
  if (data.password !== data.passwordRepeat) {
    data.passwordMatchError = "Пароли не совпадают";
    return;
  }

  data.isLoading = true;
  userStore.sessionTools.registerLogin(data.login, data.password).then(result => {
    if (result.status === "success") {
      data.step = 4;
      return;
    }

    if (result.data === "login occupied")
      data.registerError = "Логин уже занят";
    else if (result.data === "empty login")
      data.registerError = "Передан пустой логин";
    else if (result.data === "empty password")
      data.registerError = "Передан пустой пароль";
    else if (result.data === "login not reserved")
      data.registerError = "Попытка регистрации незарезервированного логина";
    else if (result.data === "already logged in")
      data.registerError = "тка регистрации после входа в систему";
    else {
      data.registerError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
      console.log("error", result.data);
    }
  }).catch(error => {
    data.registerError = "Возникла непредвиденная ошибка, попробуйте зайти позже";
    console.log("error", error);
  }).finally(() => {
    data.isLoading = false;
  });
}

function registrationComplete() {
  data.step = 1;
  tryLogin();
}
</script>

<template>
  <v-dialog v-model="isOpen" class="dialog-size">
    <v-card>
      <v-window v-model="data.step">
        <v-window-item :value="1">
          <v-card-text>
            <div class="form-fields">
              <div class="title-big mb-5">
                Войдите в аккаунт, чтобы начать учиться
              </div>
              <label class="mt-2 mb-2 d-block label" for="email">Почта</label>
              <input id="email" name="email" type="text" class="input" v-model="data.login">
              <div class="error-text" v-if="data.loginError !== ''">{{ data.loginError }}</div>

              <label class="mt-2 mb-2 d-block label" for="password">Пароль</label>
              <input id="password" name="password" type="password" class="input" v-model="data.password">
              <div class="error-text" v-if="data.passwordError !== ''">{{ data.passwordError }}</div>

              <!-- TODO: доделать функционал кнопки  -->
              <div class="text-summer-sky password-reset mt-2">Не помню пароль</div>
            </div>

            <div class="error-text text-center" v-if="data.commonError !== ''">{{ data.commonError }}</div>
            <div>
              <v-btn color="#2D9CDB" :disabled="data.isLoading" class="action-button mb-2 text-none" elevation="0"
                     @click="tryLogin">
                <span v-if="!data.isLoading">Войти</span>
                <span v-else><v-progress-circular indeterminate></v-progress-circular></span>
              </v-btn>
            </div>
            <div>
              <v-btn class="action-button mb-2 text-none border-summer-sky" elevation="0" @click="data.step = 2">
                <span class="text-summer-sky">Зарегистрироваться</span>
              </v-btn>
            </div>
          </v-card-text>
        </v-window-item>

        <v-window-item :value="2">
          <v-card-text>
            <div class="title-small mb-5">Для регистрации подойдёт адрес на любом почтовом сервисе</div>
            <div class="form-fields">
              <label class="mt-2 mb-2 d-block label" for="reg">Введите ваш email</label>
              <input id="reg" name="reg" type="text" class="input" v-model="data.login">
              <div class="error-text" v-if="data.checkLoginError !== ''">{{ data.checkLoginError }}</div>
            </div>

            <v-btn color="#2D9CDB" :disabled="data.isLoading" class="action-button mb-2 text-none" elevation="0"
                   @click="checkLogin">
              <span v-if="!data.isLoading">Далее</span>
              <span v-else><v-progress-circular indeterminate></v-progress-circular></span>
            </v-btn>
          </v-card-text>
        </v-window-item>

        <v-window-item :value="3">
          <v-card-text>
            <div class="title-small mb-5">Придумайте надежный пароль</div>
            <div class="form-fields">
              <label class="mt-2 mb-2 d-block label" for="pwd">Пароль</label>
              <input id="pwd" name="pwd" type="password" class="input" v-model="data.password">

              <label class="mt-2 mb-2 d-block label" for="pwd-confirm">Повторите пароль</label>
              <input id="pwd-confirm" name="pwd-confirm" type="password" class="input" v-model="data.passwordRepeat">
              <div class="error-text" v-if="data.passwordMatchError !== ''">{{ data.passwordMatchError }}</div>
            </div>

            <div class="error-text text-center" v-if="data.registerError !== ''">{{ data.registerError }}</div>
            <v-btn color="#2D9CDB" :disabled="data.isLoading" class="action-button mb-2 text-none" elevation="0"
                   @click="register">
              <span v-if="!data.isLoading">Зарегистрироваться</span>
              <span v-else><v-progress-circular indeterminate></v-progress-circular></span>
            </v-btn>
          </v-card-text>
        </v-window-item>

        <v-window-item :value="4">
          <v-card-text>
            <div class="form-fields">
              <div class="title-big mb-5 title-success">
                <div>
                  <img src="../../assets/images/tick.png" alt="tick" class="tick-image">
                </div>
                <div>
                  <span class="ml-5">Теперь вы готовы учиться</span>
                </div>
              </div>
              <div class="title-small mb-5">Мы отправили письмо с подтверждением на почту</div>
            </div>
            <v-btn color="#2D9CDB" class="action-button mb-2 text-none" elevation="0" @click="registrationComplete">
              Войти в аккаунт
            </v-btn>
          </v-card-text>
        </v-window-item>
      </v-window>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.title-big {
  font-size: 22px;
  /*font-weight: 500;*/
  line-height: 27px;
}

.title-small {
  font-size: 16px;
 /* font-weight: 500;*/
  line-height: 20px;
}

.dialog-size {
  min-width: 300px;
  max-width: 600px;
}

.label {
  font-size: 14px;
  /*font-weight: 500;*/
  line-height: 17px;
}

.input {
  border: 1px solid #BDBDBD;
  width: 100%;
  height: 30px;
  padding: 2px 10px 2px 10px;
  font-size: 16px;
  /*font-weight: 500;*/
  line-height: 20px;

}

.password-reset {
  font-size: 14px;
  /*font-weight: 500;*/
  line-height: 17px;
}

.action-button {
  width: 100%;
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.border-summer-sky {
  border: 1px solid #2D9CDB;
}

.form-fields {
  margin-bottom: 50px;
}

.tick-image {
  width: 70px;
}

.title-success {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
}

.error-text {
  color: #EB5757;
}
</style>