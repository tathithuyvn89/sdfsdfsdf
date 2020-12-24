<template>
  <div />
</template>

<script>
import { Loading } from 'element-ui';
import { csrf } from '@/api/auth';
export default {
  data(){
    return {
      loadingInstance: {},
    };
  },
  created(){
    this.deleteCookies();
    sessionStorage.clear();
    localStorage.clear();
    this.loadingInstance = Loading.service({ fullscreen: true, text: 'Authenticating' });
    var param = this.$route.query.m;
    if (param !== '' || param === undefined) {
      this.handleLogin(param);
    } else {
      this.$router.push({ path: '/', query: this.otherQuery }, onAbort => {});
      this.loadingInstance.close();
    }
  },
  methods: {
    deleteCookies() {
      var allCookies = document.cookie.split(';');
      for (var i = 0; i < allCookies.length; i++) {
        document.cookie = allCookies[i] + '=;expires=' + new Date(0).toUTCString();
      }
    },
    handleLogin(param) {
      this.loading = true;
      csrf().then(() => {
        this.$store.dispatch('user/loginAzure', param)
          .then(() => {
            this.loadingInstance.close();
            if (localStorage.getItem('isUser') === '1') {
              this.$store.dispatch('settings/changeSetting', {
                key: 'tagsView',
                value: false,
              });
              this.$router.push({ path: '/', query: this.otherQuery }, onAbort => {});
            } else {
              this.$router.push({ path: this.redirect || '/', query: this.otherQuery }, onAbort => {});
            }

            this.loading = false;
          })
          .catch(() => {
            this.loadingInstance.close();
            this.$router.push({ path: '/', query: this.otherQuery }, onAbort => {});
          });
      });
    },
  },
};
</script>

<style>

</style>
