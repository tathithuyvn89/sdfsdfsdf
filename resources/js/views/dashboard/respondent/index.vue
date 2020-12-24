<template>
  <div class="dashboard-respondent-container">
    <el-row>
      <h3 style="margin: 0">{{ $t('respondent.dashboard.tSelectLang') }}</h3>
      <h3 style="margin: 0">{{ $t('respondent.dashboard.tSelectLangEng') }}</h3>
    </el-row>
    <el-row class="btn-selectlanguage">
      <el-col :span="11">
        <el-button class="btn" @click="chooseLange(JA)">
          <span id="btn-chooseLang">
            {{ $t('respondent.dashboard.btnJA') }}
          </span>
        </el-button>
      </el-col>
      <el-col :span="2">
        <div class="space" />
      </el-col>
      <el-col :span="11">
        <el-button class="btn" @click="chooseLange(EN)">
          <span id="btn-chooseLang">
            {{ $t('respondent.dashboard.btnEN') }}
          </span>
        </el-button>
      </el-col>
    </el-row>
    <div class="company-guide">
      <span>
        Please be aware that any personal data that you and others provide
        through this tool may be processed by Accenture. The protection of your
        personal data is very important to Accenture. Accenture is committed to
        keeping your personal data secure, and processing it in accordance with,
        applicable data protection laws and our internal policies, including
        <a
          href="https://policies.accenture.com/Pages/0001-0100/0090.aspx?rd=1&Country=United+Kingdom"
        >Accenture’s Global Data Privacy Policy 0090.</a>

        Before providing any personal data through this tool, Accenture invites
        you to carefully read its
        <a
          href="https://in.accenture.com/protectingaccenture/data-security/5422-2/"
        >privacy statement</a>, which includes important information on why and how Accenture is
        processing your personal data.
      </span>
    </div>
  </div>
</template>
<script>
import { getFullInfo } from '@/api/respondent';
import { CLASS, LANG } from '@/constant_keys';
import { LANG_SELECT } from '@/constant_values';
export default {
  name: 'DashboardRespondent',
  data() {
    return {
      listClass: [],
      listIdClass: [],
      JA: [LANG_SELECT.JA].toString(),
      EN: [LANG_SELECT.EN].toString(),
    };
  },
  created() {
    this.$store.getters.sidebar.opened = false;
  },
  methods: {
    chooseLange(lang) {
      if (
        lang === [LANG_SELECT.JA].toString() ||
        lang === [LANG_SELECT.EN].toString()
      ) {
        this.setLang(lang);
        var params = {
          [LANG.KEY_LANGUAGE]: lang,
        };
        getFullInfo(params).then((result) => {
          this.listClass = result.classes;
          this.listClass.forEach((index) =>
            this.listIdClass.push(index.class_id)
          );
          this.$store.state.respondent.CLASS_SELECT = this.listIdClass[0];
          this.$store.dispatch('respondent/changeSelection', {
            key: [CLASS.KEY_ID],
            value: this.listIdClass[0],
          });
          if (this.listClass.length === 1) {
            this.$router.push(
              { path: '/dashboard/surveyselector', query: this.otherQuery },
              (onAbort) => {}
            );
          } else {
            this.$router.push(
              { path: '/dashboard/classselector', query: this.otherQuery },
              (onAbort) => {}
            );
          }
          this.$store.dispatch('respondent/storeSelections', [
            {
              key: 'ALL_CLASSES',
              value: this.listClass,
            },
            {
              key: 'ALL_ID_CLASS',
              value: this.listIdClass,
            },
          ]);
        });
      } else {
        this.$message({
          message: this.$t('respondent.dashboard.popup.message'),
          type: 'warning',
        });
      }
    },
    setLang(lang) {
      if (lang === 'en') {
        document.documentElement.style.setProperty(
          '--fontWeb',
          'Graphik Web Regular'
        );
      } else if (lang === 'ja') {
        document.documentElement.style.setProperty(
          '--fontWeb',
          'Hiragino Sans, Noto Sans JP'
        );
      }
      this.$store.state.respondent.LANG_SELECT = lang;
      this.$root.$i18n.locale = this.$store.state.respondent.LANG_SELECT;
      this.$store.dispatch('app/setLanguage', lang);
    },
  },
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.btn-selectlanguage {
  margin: 32px auto;
  max-width: 900px;
}
.company-guide {
  margin: 32px auto;
  border: 1px solid rgb(139, 139, 139);
  padding: 13px 7px 8px 16px;
  font-size: 13px;
  max-width: 900px;
  background-color: #fff;
  line-height: 1rem;
}
a:hover,
link {
  text-decoration: underline;
  color: #1709d3;
}
a {
  color: #1709d3;
}
h3 {
  text-align: center;
}
.dashboard-respondent-container {
  // text-align: center;
  background-color: #e3e3e3;
  min-height: 100vh;
  padding: 100px 30px 0px;
}
.space {
  padding: 10px;
}
.btn {
  width: 100%;
  background-color: #a100ff;
  border: none;
  color: white;
}
#btn-chooseLang {
  width: 70px;
  display: inline-block;
  white-space: normal;
}
</style>
