<template>
  <el-dropdown trigger="click" class="international" @command="handleSetLanguage">
    <div aria-label="Icon Select Lang">
      <svg-icon class-name="international-icon" icon-class="language" />
    </div>
    <el-dropdown-menu slot="dropdown">
      <el-dropdown-item :disabled="language==='en'" command="en">
        English
      </el-dropdown-item>
      <el-dropdown-item :disabled="language==='ja'" command="ja">
        Japanese
      </el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
export default {
  computed: {
    language() {
      return this.$store.getters.language;
    },
  },
  watch: {
    language() {
      var lang = this.$store.getters.language;
      if (lang === 'en') {
        document.documentElement.style.setProperty('--fontWeb', 'Graphik Web Regular');
      } else if (lang === 'ja') {
        document.documentElement.style.setProperty('--fontWeb', 'Hiragino Sans, Noto Sans JP');
      }
    },
  },
  methods: {
    handleSetLanguage(lang) {
      this.$i18n.locale = lang;
      this.$store.dispatch('app/setLanguage', lang);
      this.$message({
        message: 'Switch Language Success',
        type: 'success',
      });
    },
  },
};
</script>

<style scoped>
.international-icon {
  font-size: 20px;
  cursor: pointer;
  vertical-align: -5px!important;
}
</style>

