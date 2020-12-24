<template>
  <div class="app-container">
    <center>

      <div class="status-answer">
        <div v-if="check == true"> {{ $t('respondent.quizing.correctAnswer') }}</div>
        <div v-else> {{ $t('respondent.quizing.incorrectAnswer') }}</div>
      </div>
      <div class="imageExplain">
        <!-- <el-image :src="imageexplain" fill /> -->
        <img :src="imageexplain" alt="" class="imageExplain">
      </div>
      <div id="pre_explain" v-html="explainObjList" />
      <div v-if="isLastQuiz != true">
        <div v-if="check == true">
          <el-button class="btnCorrect" @click="backToQuiz">{{
            $t('respondent.quizing.button.goToNext')
          }}</el-button>
        </div>
        <div v-if="check != true">
          <el-button class="btnIncorrect" @click="backToQuiz">{{
            $t('respondent.quizing.button.tryAgain')
          }}</el-button>
        </div>
      </div>
      <div v-if="isLastQuiz == true">
        <el-button class="btnIncorrect" @click="backToQuiz">
          {{ $t('respondent.quizing.button.toComplete') }}
        </el-button>
      </div>
    </center>
  </div>
</template>

<script>
export default {
  name: 'Explain',
  props: {
    imageexplain: {
      type: String,
      default: '',
    },
    textexplain: {
      type: String,
      default: '',
    },
    check: {
      type: Boolean,
      default: false,
    },
    isLastQuiz: {
      type: Boolean,
      default: false,
    },

  },
  computed: {
    explainObjList() {
      return this.textexplain.replace(/\[([^\]]*?)\]\((.*?)\)/g, '<a target="_blank" href = "$2">$1</a>');
    },
  },
  created(){
    window.scrollTo(0, 0);
  },
  methods: {
    backToQuiz() {
      this.$emit('backToQuizComp');
    },
    findIndexOfCharacter(string, charA) {
      var indices = [];
      for (let i = 0; i < string.length; i++) {
        if (string[i] === charA) {
          indices.push(i);
        }
      }
      return indices;
    },
  },
};
</script>

<style lang="scss" scoped>
.imageExplain {
  margin-bottom: 20px;
  margin-top: 20px;
  object-fit: contain;
  width: 100%;
  max-width: 300px;
}
.content-explain {
  margin-top: 0px !important;
  margin-bottom: 0px !important;
  font-size: 1rem;
}
pre{
    margin-bottom: 0;
    margin-top: 0;
}
a{
 text-decoration: underline;
  color: #008eff;
  font-size: 1rem;
}
.textExplain{
  font-size: 1rem;
  margin-top: 0px !important;
  margin-bottom: 0px !important;
  font-weight: bold;
}
.textExplain:hover {
  text-decoration: underline;
  color: #008eff;
  font-size: 1rem;
  font-weight: bold;
}
.btnCorrect {
  height: 50px;
  width: 200px;
  background-color: #a100ff;
  border:none;
  color: white;
  font-weight: bold;
  margin-top: 20px;
}
.btnIncorrect {
  height: 50px;
  width: 200px;
  background-color: #a100ff;
  border:none;
  color: white;
  font-weight: bold;
  margin-top: 20px;
}
.status-answer{
  font-size: 2rem;
  font-weight: bold;
  color: #7500C0;
}
</style>
