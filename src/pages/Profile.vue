<script setup>
import {defineModel, ref} from "vue";
import {useUserStore} from "@/stores/UserStore.js";

const userStore = useUserStore();

let tab = defineModel({default: 1});
let data = [
  {id: 'first', value: 1, title: 'Личная информация', checked: true},
  {id: 'second', value: 2, title: 'Авторизация', checked: false},
  {id: 'third', value: 3, title: 'E-mail', checked: false},
]

const info = ref({});
userStore.session.profile
    .then(result => result.value)
    .then(result => info.value = result);

// TODO: сделать логику сохранения
</script>

<template>
  <div class="mb-5">
    <div class="title mt-5">
      Настройки профиля
    </div>
    <div class="tab">
      <template v-for="item in data">
        <input type="radio"
               :id="item.id"
               :value="item.value"
               :checked="item.checked"
               v-model="tab">
        <label :for="item.id">{{ item.title }}</label>
      </template>
    </div>
    <div>
      <v-window v-model="tab">
        <v-window-item :value="1">
          <div class="card">
            <div class="user-picture">
              <button class="change-user-picture"></button>
            </div>
            <div class="user-info">
              <div>
                <div>
                  <label>Фамилия</label>
                </div>
                <input :value="info?.lastName" type="text" placeholder="Фамилия" class="input mb-6 mt-1">
              </div>

              <div>
                <div>
                  <label>Имя</label>
                </div>
                <input :value="info?.firstName" type="text" placeholder="Имя" class="input mb-6 mt-1">
              </div>

              <div>
                <div>
                  <label>Пол</label>
                </div>
                <input :value="info?.gender" type="text" placeholder="Пол" class="input mb-6 mt-1">
              </div>

              <div>
                <div>
                  <label>Дата рождения</label>
                </div>
                <input :value="info?.birthDate" type="text" placeholder="Дата рождения" class="input mb-6 mt-1">
              </div>

              <div>
                <div>
                  <label>Номер телефона</label>
                </div>
                <input :value="info?.phone" type="text" placeholder="Номер телефона" class="input mb-6 mt-1">
              </div>
            </div>
            <div class="save-btn-right">
              <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                Сохранить
              </v-btn>
            </div>
          </div>
        </v-window-item>
        <v-window-item :value="2">
          <div class="cards">
            <div class="card">
              <div class="small-title">Изменить почтовый адрес</div>
              <div class="user-info">
                <div>
                  <div>
                    <label>Почта</label>
                  </div>
                  <input :value="info?.email" type="text" placeholder="Почта" class="input mb-6 mt-1">
                </div>

                <div>
                  <div>
                    <label>Пароль</label>
                  </div>
                  <input type="text" placeholder="Пароль" class="input mb-6 mt-1">
                </div>
              </div>
              <div class="save-btn">
                <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                  Сохранить
                </v-btn>
              </div>
            </div>
            <div class="card">
              <div class="small-title">Смена пароля</div>
              <div class="user-info">
                <div>
                  <div>
                    <label>Текущий пароль</label>
                  </div>
                  <input type="text" placeholder="Текущий пароль" class="input mb-6 mt-1">
                </div>

                <div>
                  <div>
                    <label>Новый пароль</label>
                  </div>
                  <input type="text" placeholder="Новый пароль" class="input mb-6 mt-1">
                </div>

                <div>
                  <div>
                    <label>Повторите новый пароль</label>
                  </div>
                  <input type="text" placeholder="Повторите новый пароль" class="input mb-6 mt-1">
                </div>
              </div>
              <div class="save-btn">
                <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                  Сохранить
                </v-btn>
              </div>
            </div>
          </div>
        </v-window-item>
        <v-window-item :value="3">
          <div class="card">
            <div class="small-title">E-mail</div>
            <div class="w100">
              <input :checked="info?.mailingSettings?.digest" type="checkbox" class="custom-checkbox">
              <label class="custom-checkbox-label">Дайджест: новости, подборки, скидки</label>
            </div>
            <div class="w100">
              <input :checked="info?.mailingSettings?.eventsAgenda" type="checkbox" class="custom-checkbox">
              <label class="custom-checkbox-label">Афиша событий и конференция</label>
            </div>
            <div class="w100">
              <input :checked="info?.mailingSettings?.educationalMaterials" type="checkbox" class="custom-checkbox">
              <label class="custom-checkbox-label">Полезные материалы для обучения, анонсы новых курсов</label>
            </div>
            <div class="w100">
              <input :checked="info?.mailingSettings?.submissionDeadlines" type="checkbox" class="custom-checkbox">
              <label class="custom-checkbox-label">Оповещения о сроках сдачи работ и подсказки, что делать в первую
                очередь</label>
            </div>
            <div class="save-btn">
              <v-btn color="#2D9CDB" class="start-btn bg-summer-sky text-white mt-2 text-none">
                Сохранить
              </v-btn>
            </div>
          </div>
        </v-window-item>
      </v-window>
    </div>
  </div>
</template>

<style scoped>
.custom-checkbox-label {
  padding-left: 20px;
  font-size: 18px;
  font-weight: 500;
  line-height: 24px;
  letter-spacing: 0;
}

.custom-checkbox {
  width: 17px;
  height: 17px;
}

.w100 {
  width: 100%;
}

.save-btn {
  width: 100%;
}

.cards {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.small-title {
  font-size: 24px;
  font-weight: 600;
  line-height: 29px;
  letter-spacing: 0;
  width: 100%;
}

.save-btn-right {
  width: 90%;
  text-align: right;
}

.user-info {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 40%));
  column-gap: 20px;
  width: 80%;
}

.input {
  border: 1px solid gray;
  border-radius: 5px;
  margin-right: 10px;
  padding-left: 7px;
  height: 30px;
  width: 300px;
}

.start-btn {
  border-radius: 5px;
  padding: 2px 20px 2px 20px;
  font-size: 16px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0;
}

.change-user-picture {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  background-image: url("@/assets/images/change-profile-picture.png");
  position: absolute;
  bottom: 0;
}

.user-picture {
  position: relative;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  background-image: url("@/assets/images/profile-picture.png");
}

.card {
  background-color: #F6F8F9;
  padding: 20px;
  border-radius: 6px;
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.title {
  font-size: 40px;
  font-weight: 600;
  line-height: 49px;
  letter-spacing: 0;
}

.tab {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}

.tab > input[type="radio"] {
  display: none;
}

.tab > label {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  cursor: pointer;
  transition: color .15s ease-in-out, border-color .15s ease-in-out;
  background: 0 0;
  border-bottom: 0.125rem solid transparent;
  font-size: 18px;
  font-weight: bold;
  line-height: 24px;
  letter-spacing: 0;
}

.tab > label:hover {
  border-bottom-color: black;
}

.tab > input[type="radio"]:checked + label {
  cursor: default;
  color: #2D9CDB;
  border-bottom-color: #2D9CDB;
}
</style>