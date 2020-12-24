<template>
  <div class="dashboard-respondent-container">
    <el-row>
      <h3>{{ $t('respondent.selectClass.title') }}</h3>
    </el-row>
    <el-row v-for="(aClass, index) in listClass" :key="index">
      <el-button
        class="classButton"
        type="primary"
        @click="chooseClass(aClass.class_id)"
      >
        <span class="className">{{ aClass.class_name }}</span>
      </el-button>
    </el-row>
    <el-row>
      <el-button
        id="__comeback"
        class="comeback"
        type="danger"
        @click="comeback()"
      >Back</el-button>
    </el-row>
    <!-- Dialog Waring not select Lang -->
    <el-dialog
      type="danger"
      :title="$t('respondent.selectClass.popup.title')"
      width="250px"
      :visible.sync="dialogWarningLang"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
      center
    >
      <center>
        <span>{{ $t('respondent.selectClass.popup.message') }}</span>
      </center>
      <span slot="footer" class="dialog-footer">
        <el-button id="warning-not-choose-language" @click="closeWarningLang()">{{
          $t('respondent.selectClass.popup.back')
        }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getFullInfo } from '../../api/respondent';
import { CLASS, DEPARTMENT, TAG, LANG } from '../../constant_keys';
import { LANG_SELECT } from '../../constant_values';
export default {
  name: 'SelectClass',
  components: {},
  data() {
    return {
      listClass: [],
      listIdClass: [],
      dialogWarningLang: false,
    };
  },
  created() {
    this.$store.getters.sidebar.opened = false;
    if (this.IsExistLang()) {
      this.getInfo();
    }
  },
  methods: {
    async getInfo() {
      this.listClass = [];
      this.listIdClass = [];
      try {
        var lang = this.$store.state.respondent.LANG_SELECT;
        var params = {
          [LANG.KEY_LANGUAGE]: lang,
        };
        const response = await getFullInfo(params);
        this.listClass = response[CLASS.KEY_LIST];
        this.listIdClass = this.listClass.map(a => a.class_id);
        var deparments = response[DEPARTMENT.KEY_LIST];
        var tags = response[TAG.KEY_LIST];
        this.$store.dispatch('respondent/storeSelections', [
          {
            key: 'ALL_DEPARTMENTS',
            value: deparments,
          },
          {
            key: 'ALL_TAGS',
            value: tags,
          },
        ]);
      } catch (error) {
        console.log(error);
      }
    },
    IsExistLang() {
      const Lang = this.$store.state.respondent.LANG_SELECT;
      if (
        Lang === [LANG_SELECT.JA].toString() ||
        Lang === [LANG_SELECT.EN].toString()
      ) {
        return true;
      } else {
        this.dialogWarningLang = true;
        return false;
      }
    },
    closeWarningLang() {
      this.dialogWarningLang = false;
      this.$router.push(
        { path: '/dashboard/index', query: this.otherQuery },
        (onAbort) => {}
      );
    },
    chooseClass(val) {
      if (this.listIdClass.includes(val) === true) {
        this.setClass(val);
        this.$router.push(
          { path: '/dashboard/surveyselector', query: this.otherQuery },
          (onAbort) => {}
        );
      } else {
        this.$message({
          message: this.$t('respondent.selectClass.mesage'),
          type: 'warning',
        });
      }
    },
    setClass(val) {
      this.$store.state.respondent.CLASS_SELECT = val;
      this.$store.dispatch('respondent/changeSelection', {
        key: [CLASS.KEY_ID],
        value: val,
      });
    },
    comeback() {
      this.$router.push(
        { path: '/dashboard/index', query: this.otherQuery },
        (onAbort) => {}
      );
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.classButton {
  width: 50vw;
  max-width: 450px;
  margin: auto;
  overflow: hidden;
  text-overflow: ellipsis;
  word-break: break-all;
  background-color:  #a100ff;
  border-color:  #a100ff;
}
.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}
.dashboard-respondent-container {
  text-align: center;
  background-color: #e3e3e3;
  min-height: 100vh;
  padding: 100px 60px 0px;
}
.comeback {
  width: 50vw;
  max-width: 450px;
  margin: auto;
}
.className {
  width: 100%;
  overflow: hidden;
  margin: auto;
  text-overflow: ellipsis;
}
#__comeback, #warning-not-choose-language {
  background-color:#969696 ;
  border-color: #969696;
  color: #FFFFFF;
}
</style>
