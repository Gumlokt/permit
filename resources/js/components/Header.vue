<template>
  <header class="header">
    <nav class="menu">
      <img :src="logoImg" class="menu__logo" />

      <ul class="menu__items">
        <li class="menu__item">
          <a href="#" class="menu__link menu__link_active"><span class="material-icons-outlined md-18">home</span> Главная</a>
        </li>
        <li class="menu__item">
          <a href="#" class="menu__link"><span class="material-icons-outlined md-18">search</span>Поиск</a>
        </li>
        <li class="menu__item">
          <a href="#" class="menu__link"><span class="material-icons-outlined md-18">bar_chart</span> Статистика</a>
        </li>
      </ul>

      <div class="menu__user">
        <a href="#" class="menu__link"><span class="material-icons-outlined md-18">settings</span> Настройки</a>
      </div>
    </nav>
  </header>
</template>

<script>
import logoImg from '../../images/logo.svg';

export default {
  data() {
    return {
      logoImg: logoImg
    }
  }
};
</script>

<style>
.header {
  padding: 10px 0;
  background-image:
    linear-gradient(178deg, rgb(0, 138, 255), rgba(0, 138, 255, 0) 100%),
    linear-gradient(20deg, rgb(0, 255, 198), rgba(0, 255, 198, 0) 100%),
    linear-gradient(120deg, rgb(0, 255, 198), rgba(0, 255, 198, 0) 100%);

  border-bottom: 1px solid #aaa;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
}

.menu {
  margin: 0 auto;
  max-width: 1280px;

  display: grid;
  gap: 20px;
  grid-template-columns: min-content auto max-content;
  grid-template-areas: "logo nav user";
}

.menu__logo {
  grid-area: logo;
  place-self: center;
  max-width: 40px;
}

.menu__items {
  grid-area: nav;

  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: max-content;
}

.menu__user {
  grid-area: user;
  display: flex;
}

.menu__item {
  display: flex;
}

.menu__link {
  margin: 0 auto;
  padding: 0 20px;
  border-radius: 8px;
  text-decoration: none;
  opacity: 1;

  font-size: 16px;
  line-height: 20px;
  text-decoration: none;
  color: #fff;
  text-shadow: 0 1px 0 #555;

  overflow: hidden;
  position: relative;
  z-index: 1;

  display: flex;
  align-items: center;

  transition: opacity .1s linear;
}

.menu__link::after {
  content: '';
  position: absolute;
  z-index: -1;
  left: calc((100% + 4rem) * -1);
  top: 0%;
  bottom: 0%;
  width: calc(100% + 4rem);
  clip-path: polygon(0% 0%, 100% 0%, calc(100% - 4rem) 100%, 0% 100%);
  background: #52b7fa;
  opacity: 0;
  transition: all 250ms ease-in-out;
}

.menu__link:hover::after {
  left: 0%;
  opacity: 1;
}

.menu__link:hover {
  cursor: pointer;
  transform: scale(1.1, 1.1);
}

.menu__link_active {
  color: #ff7;
  text-shadow: 0 1px 0 #555;
}
</style>
