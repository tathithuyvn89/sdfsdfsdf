<template>
  <div class="app-container">
    <center>
      <h4>{{ textcomplete }}</h4>
      <el-row>
        <el-col :span="24">
          <el-button class="btn" @click="toAgain">{{
            $t('respondent.quizing.button.doAgain')
          }}</el-button>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24">
          <el-button class="btn" @click="toSurvey">{{
            $t('respondent.quizing.button.toSurvey')
          }}</el-button>
        </el-col>
      </el-row>
      <el-row v-if="lengthClasses > 1">
        <el-col :span="24">
          <el-button class="btn" @click="toClass">{{
            $t('respondent.quizing.button.toClass')
          }}</el-button>
        </el-col>
      </el-row>
      <!-- Log out -->
      <!-- <el-row>
        <el-col :span="24">
          <el-button class="logout" type="danger" @click="toLogout()">
            {{ $t('respondent.quizing.button.toLogout') }}</el-button
          >
        </el-col>
      </el-row> -->
    </center>
  </div>
</template>

<script>
export default {
  name: 'Complete',
  props: {
    textcomplete: {
      type: String,
      default: '',
    },
  },
  computed: {
    lengthClasses() {
      return new Set(this.$store.state.respondent.ALL_ID_CLASS).size;
    },
  },
  methods: {
    toAgain() {
      this.$emit('toAgain');
    },
    toSurvey() {
      this.$router.push(
        { path: '/dashboard/surveyselector', query: this.otherQuery },
        (onAbort) => {}
      );
    },
    toClass() {
      if (this.$store.state.respondent.ALL_CLASSES.length > 1) {
        this.$store.dispatch('respondent/changeSelection', {
          key: 'ALL_CLASSES',
          value: [],
        });
        this.$router.push(
          { path: '/dashboard/classselector', query: this.otherQuery },
          (onAbort) => {}
        );
      } else {
        this.$store.dispatch('respondent/changeSelection', {
          key: 'ALL_CLASSES',
          value: [],
        });
        this.$router.push(
          { path: '/dashboard/index', query: this.otherQuery },
          (onAbort) => {}
        );
      }
    },
    // async toLogout() {
    //   await this.$store.dispatch('user/logout');
    //   this.$router.push(
    //     { path: '/respondent', query: this.otherQuery },
    //     (onAbort) => {}
    //   );
    //   // this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    // },
  },
};
</script>

<style lang="scss" scoped>
.btn {
  margin-top: 20px;
  margin-bottom: 20px;
  width: 320px;
  height: 50px;
  background: #a100ff;
  color: white;
}
// .logout {
//   float: right;
//   margin-right: 10px;
//   background-color: rgb(207, 10, 10);
//   border: none;
//   outline: none;
//   cursor: pointer;
// }
</style>
