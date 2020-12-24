<template>
  <div>
    <div v-if="isUser != 'user'" class="navbar">
      <hamburger
        id="hamburger-container"
        :is-active="sidebar.opened"
        class="hamburger-container"
        @toggleClick="toggleSideBar"
      />
      <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />
      <div class="right-menu">
        <template v-if="device !== 'mobile'">
          <search id="header-search" class="right-menu-item" />
          <screenfull id="screenfull" class="right-menu-item hover-effect" />
          <el-tooltip
            :content="$t('navbar.size')"
            effect="dark"
            placement="bottom"
          >
            <size-select
              id="size-select"
              class="right-menu-item hover-effect"
            />
          </el-tooltip>
          <lang-select class="right-menu-item hover-effect" />
        </template>
        <el-dropdown
          class="avatar-container right-menu-item hover-effect"
          trigger="click"
        >
          <div class="avatar-wrapper">
            <img :src="domain + avatar" class="user-avatar" alt="user-avatar">
          </div>
          <el-dropdown-menu slot="dropdown">
            <router-link to="/dashboard/index">
              <el-dropdown-item>
                {{ $t('navbar.dashboard') }}
              </el-dropdown-item>
            </router-link>
            <router-link v-show="userId !== null" :to="`/dashboard/profile`">
              <el-dropdown-item>
                {{ $t('navbar.profile') }}
              </el-dropdown-item>
            </router-link>
            <el-dropdown-item divided>
              <span style="display: block" @click="logout">
                {{ $t('navbar.logOut') }}
              </span>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
    <div v-if="isUser == 'user'" class="header__user">
      <img :src="imageLogo" class="header__user-logo" alt="logo">
      <span class="header__user-title">{{ title }}</span>
    </div>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import Breadcrumb from '@/components/Breadcrumb';
import Hamburger from '@/components/Hamburger';
import Screenfull from '@/components/Screenfull';
import SizeSelect from '@/components/SizeSelect';
import LangSelect from '@/components/LangSelect';
import Search from '@/components/HeaderSearch';
import imageLogo from '@/assets/logo_images/Acc_Logo_All_White_RGB.png';
export default {
  components: {
    Breadcrumb,
    Hamburger,
    Screenfull,
    SizeSelect,
    LangSelect,
    Search,
  },
  data() {
    return {
      domain: process.env.MIX_STORE_IMAGE_URL + 'avatars/',
      isUser: this.$store.getters.roles.toString(),
      imageLogo: imageLogo,
      title: process.env.MIX_APP_TITLE,
    };
  },
  computed: {
    ...mapGetters(['sidebar', 'name', 'avatar', 'device', 'userId']),
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    async logout() {
      if (this.isUser === 'user') {
        await this.$store.dispatch('user/logout');
        this.$router.push(
          { path: '/respondent', query: this.otherQuery },
          (onAbort) => {}
        );
      } else {
        await this.$store.dispatch('user/logout');
        this.$router.push(`/login?redirect=${this.$route.fullPath}`);
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.header__user {
  height: 78px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0px 30px;
  background-color: #7500c0;
}
.header__user-logo {
  width: 200px;
  height: 52px;
  background: transparent;
}

.header__user-title {
  position: relative;
  top: 11px;
  font-size: 2.4rem;
  font-weight: 600;
  color: #fff;
}
@media screen and (max-width: 720px) {
  .header__user {
    height: 48px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0px 16px;
    background-color: #7500c0;
  }
  .header__user-logo {
    width: 115px;
    height: 30px;
    background: transparent;
  }

  .header__user-title {
    position: relative;
    line-height: 1rem;
    top: 7.5px;
    font-size: 1.2rem;
    font-weight: 900;
    color: #fff;
  }
}
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background 0.3s;
    -webkit-tap-highlight-color: transparent;
    &:hover {
      background: rgba(0, 0, 0, 0.025);
    }
  }
  .breadcrumb-container {
    float: left;
  }
  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }
  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;
    &:focus {
      outline: none;
    }
    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;
      &.hover-effect {
        cursor: pointer;
        transition: background 0.3s;
        &:hover {
          background: rgba(0, 0, 0, 0.025);
        }
      }
    }
    .avatar-container {
      margin-right: 30px;
      .avatar-wrapper {
        margin-top: 5px;
        position: relative;
        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 4px;
        }
        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
