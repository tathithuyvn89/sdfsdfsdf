<template>
  <div class="main">
    <!-- <div class="login">
      <center>
        <h3 class="title">
          {{ $t('login.title') }}
        </h3>
        <el-button type="primary" @click="redirectLoginAzure()">Login with Azure (Respondent only)</el-button>
      </center>
    </div> -->
  </div>
</template>
<script>
export default {
  name: 'SingleSignOn',
  created() {
    // this.$store.getters.sidebar.opened = false;
    window.location.href = process.env.MIX_APP_DOMAIN + '/saml2/aad/login';
  },
  methods: {
    redirectLoginAzure(){
      window.location.href = process.env.MIX_APP_DOMAIN + '/saml2/aad/login';
      //  this.$router.push('qs1.vw-dev.com/saml2/aad/login');
    },
  },
};
</script>
<style scoped>
/* .main {
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: #2D3A4B;
    padding: 0;
    margin: 0;
}
.login {
  margin-top: 200px;
}
.title {
  color: white;
  font-size: 25px;
} */
</style>
