<template>
  <div>
    <p class="num-process">{{ QuizCurrent.index }}/{{ numberQuiz }}</p>
    <el-row class="process">
      <el-col :span="3" class="text-process">Start</el-col>
      <el-col :span="1"><div class="space" /></el-col>
      <el-col :span="17">
        <el-progress
          :percentage="current_point"
          color="#7500c0"
          :show-text="false"
        />
      </el-col>
      <el-col :span="1"><div class="space" /></el-col>
      <el-col :span="2" class="text-process">End</el-col>
    </el-row>
    <!-- Component show Quiz -->
    <QuizForQuizing
      v-if="ShowQuiz"
      :title="QuizCurrent.title"
      :image="QuizCurrent.image"
      :sentence="QuizCurrent.sentence"
      :correct-answer="QuizCurrent.CorrectAnswer"
      :options="QuizCurrent.options"
      :disable-check-btn="disableComChild"
      :select-answer-get="selectAnswerTransfer"
      @transferOptionSelect="getOptionSelect"
    />
    <Explain
      v-if="ShowExplain"
      :imageexplain="imgExplain"
      :textexplain="Explain"
      :check="checkCorrect"
      :is-last-quiz="lastQuiz"
      @backToQuizComp="getBackToQuiz"
    />
    <Complete
      v-if="ShowComplete"
      :textcomplete="QuizCurrent.complete_message"
      @toAgain="DoAgain"
    />
    <!-- Dialog Popup check select Lang -->
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
        <el-button id="__warningChooseLang" @click="closeWarningLang()">
          {{ $t('respondent.selectClass.popup.back') }}
        </el-button>
      </span>
    </el-dialog>
    <!-- Dialog Popup check select Class and Survey -->
    <el-dialog
      type="danger"
      :title="$t('respondent.quizing.dialog.titlecheck')"
      width="250px"
      :visible.sync="dialogWarningClass"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
      center
    >
      <center>
        <span v-if="isError === false">{{ messPopup }}</span>
        <span v-else>{{ this.$t('respondent.quizing.dialog.wrong') }}</span>
      </center>
      <span slot="footer" class="dialog-footer">
        <el-button id="__warningChooseClass" @click="closeWarningClass()">{{
          $t('respondent.quizing.button.backPage')
        }}</el-button>
      </span>
    </el-dialog>
    <!-- Button submit quiz -->
    <el-row v-if="ShowQuiz">
      <el-col :span="24">
        <div class="showBTN">
          <el-button
            type="primary"
            class="btnSubmit"
            :disabled="disableComChild"
            @click="submit"
          >{{ $t('respondent.quizing.button.submit') }}</el-button>
        </div>
      </el-col>
      <!-- SUSPEND BUTTON -->
      <el-col :span="24">
        <!-- <el-button type="danger" class="backBtn" @click="toBack()">
          Back
        </el-button> -->
        <el-button
          id="__suspendBtn"
          type="default"
          class="suspendBtn"
          :disabled="disableComChild"
          @click="toSuspend()"
        >{{ $t('respondent.quizing.button.toSuspend') }}</el-button>
      </el-col>
      <!-- SUSPEND DIALOG -->
      <el-dialog :visible.sync="dialogVisibleSuspend" center width="90%">
        <center>{{ $t('popup.quiz.suspend.content') }}</center>
        <div slot="footer" class="dialog-footer">
          <el-button
            id="__confirmSuspend"
            type="info"
            @click="submitConfrimSuspend()"
          >
            Yes
          </el-button>
          <el-button
            id="__cancelSuspend"
            type="info"
            @click="dialogVisibleSuspend = false"
          >
            No
          </el-button>
        </div>
      </el-dialog>
    </el-row>
  </div>
</template>

<script>
// Import component
import QuizForQuizing from '@/views/Quizing/Quiz/index.vue';
import Explain from '@/views/Quizing/Quiz/Explain.vue';
import Complete from '@/views/Quizing/Quiz/Complete.vue';
// Imprt Constant
import { STATUS_SURVEY, LANG_SELECT } from '../../constant_values';
// Import Api
import { getOneSurvey, updateStatusSurvey } from '@/api/respondent';
export default {
  components: {
    QuizForQuizing,
    Explain,
    Complete,
  },
  data() {
    return {
      // Store all Survey data there after calling the API
      dataSurvey: [],
      // Status Dialog check
      dialogWarningClass: false,
      dialogWarningLang: false,
      dialogVisibleSuspend: false,
      isError: false,
      // Path Redirect when respondent not choose class or survey
      pathRedirect: '',
      // Message when respondent not choose class or survey
      messPopup: '',
      // Data of QUIZ DOING

      QuizCurrent: {
        // Index of quiz doing
        index: 0,
        // Percent survey
        percent: 0,
        title: '',
        image: '',
        sentence: '',
        // Option of quiz current
        options: [],
        // Option selected
        optionSelect: [],

        IDoptionSelect: [],
        // Option correct
        correct: [],
        CorrectAnswer: [],
        // end test
        img_correct: '',
        img_incorrect: '',
        ex_correct: '',
        ex_incorrect: '',
        complete_message: '',
      },
      // Title of Submit: Explain correct or incorrect
      titleSubmit: '',
      // Explain of quiz current
      Explain: '',
      // IMG Explain of quiz current
      imgExplain: '',
      checkCorrect: false,
      // Array store quiz complete
      QuizDone: [],
      // Status of btn Back quiz
      disableBtnBack: false,
      // Status of btn Next quiz
      disableBtnNext: false,
      // index Quiz conplete - using for respondent view quiz complete
      indexSaveQuiz: null,
      // check action when indexSaveQuiz change (indexSaveQuiz change when complete quiz or next, back quiz complete)
      actionChangeIndex: '',
      // disable checkbox and btn submit when respondent view quiz complete
      disableComChild: false,
      // Array store option of quiz complete when respondent view quiz complete. if respondent doing quiz it transfer empty
      selectAnswerTransfer: [],
      point: 0,
      numberQuiz: 0,
      ShowQuiz: true,
      ShowExplain: false,
      ShowComplete: false,
      lastQuiz: false,
      IdSave: 0,
      domain: process.env.MIX_STORE_IMAGE_URL,
      options: [],
      times: 0,
      current_result: 0,
      Curquizid: 0,
      quizz_id_ans: 0,
      correct: 0,
      it: 1,
      listClass: [],
      listClassLength: 0,
    };
  },
  computed: {
    changeQuiz() {
      return this.QuizCurrent.index;
    },
    changeIndexSave() {
      return this.indexSaveQuiz;
    },
    current_point() {
      return parseInt(this.$store.state.app.current_point);
    },
    changeQuizzingState() {
      let a = 0;
      let b = 0;
      let c = 0;
      if (this.$store.getters.quizingState.ShowQuiz) {
        a = 1;
      }
      if (this.$store.getters.quizingState.ShowExplain) {
        b = 2;
      }
      if (this.$store.getters.quizingState.ShowComplete) {
        c = 3;
      }
      return a + b + c;
    },
  },
  watch: {
    // check quiz complete and change data
    changeQuizzingState(newValue, oldValue) {
      this.ShowQuiz = this.$store.getters.quizingState.ShowQuiz;
      this.ShowComplete = this.$store.getters.quizingState.ShowComplete;
      this.ShowExplain = this.$store.getters.quizingState.ShowExplain;
    },
    changeQuiz() {
      if (this.QuizCurrent.index < this.numberQuiz) {
        this.fetchDataChangeQuiz();
        this.times = 0;
        this.lastQuiz = false;
      } else if (this.QuizCurrent.index === this.numberQuiz) {
        this.lastQuiz = true;
        this.updateStatus();
        this.$store.dispatch('app/setQuizingState', {
          ShowQuiz: false,
          ShowExplain: true,
          ShowComplete: false,
        });
      }
    },
  },
  created() {
    this.$store.getters.sidebar.opened = false;
    this.ShowQuiz = this.$store.getters.quizingState.ShowQuiz;
    this.ShowExplain = this.$store.getters.quizingState.ShowExplain;
    this.ShowComplete = this.$store.getters.quizingState.ShowComplete;
    // Check respondent choose class and survey: if true -> continue; if false -> redirect
    if (this.IsExistLang()) {
      if (this.IsExitClass()) {
        // Get data of Survey selected
        this.getSurvey();
        // Not complete quiz -> btn back and next is disable
        this.disableBtnBack = true;
        this.disableBtnNext = true;
      }
    }
  },
  methods: {
    // Transfer list Option selected and fetch data
    getOptionSelect(listOption) {
      this.QuizCurrent.optionSelect = listOption.position_option;
      this.QuizCurrent.IDoptionSelect = listOption.id_option;
    },
    getBackToQuiz() {
      this.QuizCurrent.optionSelect.length = 0;
      if (this.lastQuiz) {
        this.$store.dispatch('app/setQuizingState', {
          ShowQuiz: false,
          ShowExplain: false,
          ShowComplete: true,
        });
      } else {
        this.$store.dispatch('app/setQuizingState', {
          ShowQuiz: true,
          ShowExplain: false,
          ShowComplete: false,
        });
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
        this.$root.$i18n.locale = this.$store.getters.language;
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
    // Check respondent choose class and survey
    IsExitClass() {
      const IdClass = this.$store.state.respondent.CLASS_SELECT;
      const AllIdClass = this.$store.state.respondent.ALL_ID_CLASS;
      const IdSurvey = this.$store.state.respondent.SURVEY_SELECT;
      const AllIdSurvey = this.$store.state.respondent.ALL_ID_SURVEY;

      if (AllIdClass.includes(IdClass) === false) {
        this.messPopup = this.$t('respondent.quizing.dialog.notClass');
        this.dialogWarningClass = true;
        this.pathRedirect = '/dashboard/classselector';
        return false;
      } else {
        if (AllIdSurvey.includes(IdSurvey) === false) {
          this.messPopup = this.$t('respondent.quizing.dialog.notSurvey');
          this.dialogWarningClass = true;
          this.pathRedirect = '/dashboard/surveyselector';
          return false;
        } else {
          return true;
        }
      }
    },
    // When respondent close dialog warning (not choose class or survey) -> redirect
    closeWarningClass() {
      this.dialogWarningClass = false;
      this.$router.push(
        { path: this.pathRedirect, query: this.otherQuery },
        (onAbort) => {}
      );
    },
    // Get data of Survey Current
    async getSurvey() {
      const IDSurvey = this.$store.state.respondent.SURVEY_SELECT;
      // Params get Survey
      var params = {
        id: IDSurvey,
        la: this.$root.$i18n.locale,
      };
      // Call api
      var resGetSurvey = await getOneSurvey(params);
      // Fetch data
      this.dataSurvey = resGetSurvey;
      // Show quiz current (Quiz 1)
      this.fetchDataChangeQuiz();
      // Data of dialog submit quiz
      this.QuizCurrent.img_correct =
        this.domain + this.dataSurvey.survey_correct_picture;
      this.QuizCurrent.img_incorrect =
        this.domain + this.dataSurvey.survey_incorrect_picture;
      this.QuizCurrent.complete_message = this.dataSurvey.survey_complete_message;
      this.numberQuiz = this.dataSurvey.quizzes.length;
      this.point = 100 / this.numberQuiz;
    },
    RandomQuiz(ArrayQuiz) {
      for (var i = ArrayQuiz.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [ArrayQuiz[i], ArrayQuiz[j]] = [ArrayQuiz[j], ArrayQuiz[i]];
      }
      return ArrayQuiz;
    },
    // When respondent click btn submit
    submit() {
      // Prevent many message can show
      this.$message.closeAll();
      if (this.QuizCurrent.optionSelect.length === 0) {
        this.$message({
          message: this.$t('respondent.quizing.dialog.notAnswer'),
          type: 'warning',
        });
      } else if (
        this.QuizCurrent.correct.length !== this.QuizCurrent.optionSelect.length
      ) {
        this.options.splice(0);
        for (var i = 0; i < this.QuizCurrent.optionSelect.length; i++) {
          this.options.push(this.QuizCurrent.IDoptionSelect[i]);
        }
        this.$store.dispatch('app/setQuizingState', {
          ShowQuiz: false,
          ShowExplain: true,
          ShowComplete: false,
        });
        this.Explain = this.QuizCurrent.ex_incorrect;
        this.imgExplain = this.QuizCurrent.img_incorrect;
        this.correct = 0;
        this.checkCorrect = false;
        this.times++;
        this.checkUpdate();
        // this.QuizCurrent.options = this.RandomQuiz(this.QuizCurrent.options);
      } else {
        this.checkSubmit = true;
        var countCorrect = 0;
        this.options.splice(0);
        for (i = 0; i < this.QuizCurrent.optionSelect.length; i++) {
          this.options.push(this.QuizCurrent.IDoptionSelect[i]);
          for (var j = 0; j < this.QuizCurrent.correct.length; j++) {
            if (
              this.QuizCurrent.optionSelect[i] === this.QuizCurrent.correct[j]
            ) {
              countCorrect += 1;
            }
          }
        }

        if (countCorrect === this.QuizCurrent.correct.length) {
          if (this.QuizCurrent.index < this.numberQuiz) {
            this.Explain = this.QuizCurrent.ex_correct;
            this.imgExplain = this.QuizCurrent.img_correct;
            this.correct = 1;
            this.checkCorrect = true;
            this.$store.dispatch('app/setQuizingState', {
              ShowQuiz: false,
              ShowExplain: true,
              ShowComplete: false,
            });
            this.times++;
            this.current_result = this.QuizCurrent.index + 1;
            this.checkUpdate();
            this.QuizCurrent.index += 1;
            if (this.QuizCurrent.index === this.numberQuiz) {
              this.QuizCurrent.percent = 100;
              this.$store.dispatch(
                'app/setCurrentPoint',
                this.QuizCurrent.percent
              );
            }
            // this.QuizCurrent.options = this.RandomQuiz(
            //   this.QuizCurrent.options
            // );
            if (this.QuizCurrent.percent < 100) {
              this.QuizCurrent.percent += this.point;
              this.$store.dispatch(
                'app/setCurrentPoint',
                this.QuizCurrent.percent
              );
            }
          }
        } else {
          // this.QuizCurrent.options = this.RandomQuiz(this.QuizCurrent.options);
          this.$store.dispatch('app/setQuizingState', {
            ShowQuiz: false,
            ShowExplain: true,
            ShowComplete: false,
          });
          this.Explain = this.QuizCurrent.ex_incorrect;
          this.imgExplain = this.QuizCurrent.img_incorrect;
          this.checkCorrect = false;
          this.times++;
          this.correct = 0;
          this.checkUpdate();
        }
      }
    },
    DoAgain() {
      this.$store.dispatch('app/setQuizingState', {
        ShowQuiz: true,
        ShowExplain: false,
        ShowComplete: false,
      });
      this.it = 1;
      this.times = 1;
      this.current_result = 0;
      this.$store.dispatch('app/setCurrentPoint', 0);
      this.QuizCurrent.percent = 0;
      this.QuizCurrent.index = 0;
    },
    createStatus() {
      var dataUpdateStatus = {
        id: this.IdSave,
        respondent_id: this.$store.getters.userId,
        survey_id: this.$store.state.respondent.SURVEY_SELECT,
        total: this.numberQuiz,
        result: this.current_result,
        current_result: this.current_result,
        options: this.options,
        times: this.times,
        it: this.it,
        correct: this.correct,
        answered_quiz_id: this.Curquizid,
        quiz_id: this.quizz_id_ans,
      };
      return updateStatusSurvey(dataUpdateStatus);
    },
    updateStatusDoing() {
      var dataUpdateStatus = {
        id: this.IdSave,
        respondent_id: this.$store.getters.userId,
        survey_id: this.$store.state.respondent.SURVEY_SELECT,
        total: this.numberQuiz,
        result: this.current_result,
        current_result: this.current_result,
        options: this.options,
        times: this.times,
        it: this.it,
        correct: this.correct,
        answered_quiz_id: this.Curquizid,
        quiz_id: this.quizz_id_ans,
      };
      return updateStatusSurvey(dataUpdateStatus);
    },
    // Call api for update status survey: not done -> done
    updateStatus() {
      var dataUpdateStatus = {
        id: this.IdSave,
        respondent_id: this.$store.getters.userId,
        survey_id: this.$store.state.respondent.SURVEY_SELECT,
        total: this.numberQuiz,
        result: parseInt([STATUS_SURVEY.FINISH]),
        options: this.options,
        times: this.times,
        it: this.it,
        correct: this.correct,
        answered_survey_id: this.Curquizid,
        quiz_id: this.quizz_id_ans,
      };
      return updateStatusSurvey(dataUpdateStatus);
    },
    checkUpdate() {
      if (this.QuizCurrent.index === 0) {
        var resCreate = this.createStatus();
        resCreate.then((data) => {
          this.IdSave = data.id;
          this.Curquizid = data.answered_quiz_id;
          this.it = data.it;
          if (this.QuizCurrent.index === this.numberQuiz) {
            this.updateStatus();
          }
        });
      } else if (
        this.QuizCurrent.index > 0 &&
        this.QuizCurrent.index < this.numberQuiz
      ) {
        this.updateStatusDoing();
      } else if (this.QuizCurrent.index === this.numberQuiz) {
        this.updateStatus();
      }
    },
    // Change data when complete quiz and next or back quiz (indexSaveQuiz equals index quiz doing)
    fetchDataChangeQuiz() {
      this.QuizCurrent.title = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_title;
      this.quizz_id_ans = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_id;

      if (
        this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_picture !== null &&
        this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_picture !== ''
      ) {
        this.QuizCurrent.image =
          this.domain +
          this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_picture;
      } else {
        this.QuizCurrent.image = '';
      }

      this.QuizCurrent.sentence = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_sentence;
      this.QuizCurrent.options = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_options;
      // this.QuizCurrent.options = this.RandomQuiz(this.QuizCurrent.options);
      this.QuizCurrent.correct = [];
      for (
        var i = 0;
        i < this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_options.length;
        i++
      ) {
        if (
          this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_options[i]
            .option_correct === 1
        ) {
          this.QuizCurrent.correct.push(
            this.dataSurvey.quizzes[this.QuizCurrent.index].quiz_options[i]
              .option_position
          );
        }
        this.QuizCurrent.CorrectAnswer = this.QuizCurrent.correct;
      }
      this.QuizCurrent.ex_correct = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_explain_correct;
      this.QuizCurrent.ex_incorrect = this.dataSurvey.quizzes[
        this.QuizCurrent.index
      ].quiz_explain_incorrect;
      this.selectAnswerTransfer = [];
    },

    // Suspend method quiz
    toSuspend() {
      this.dialogVisibleSuspend = true;
    },
    // toBack() {
    //   this.listClassLength = this.$store.state.respondent.ALL_CLASSES.length;
    //   if (this.listClassLength === 1) {
    //     this.$store.dispatch('respondent/changeSelection', {
    //       key: 'ALL_CLASSES',
    //       value: [],
    //     });
    //     this.$router.push({ path: '/dashboard' }, (onAbort) => {});
    //   } else {
    //     this.$store.dispatch('respondent/changeSelection', {
    //       key: 'ALL_CLASSES',
    //       value: [],
    //     });
    //     this.$router.push({ path: '/classselector' }, (onAbort) => {});
    //   }
    // },

    submitConfrimSuspend() {
      this.dialogVisibleSuspend = false;
      // this.$store.dispatch('app/setQuizingState', {
      //   ShowQuiz: false,
      //   ShowExplain: false,
      //   ShowComplete: true,
      // });
      this.$router.push({ path: '/dashboard/surveyselector' }, (onAbort) => {});
    },
  },
};
</script>

<style>
</style>

<style scoped lang="scss">
.num-process {
  padding-top: 13px;
  margin: 0;
  text-align: center;
  font-size: 15px;
  background-color: #bebebe;
}
.break-text {
  width: 100%;
  line-break: auto;
  margin-top: 20px;
}

.btn-back {
  float: right;
  width: 150px;
  margin-right: 10px;
}

.btn-next {
  float: left;
  width: 150px;
  margin-left: 10px;
}
.process {
  text-align: center;
  padding-bottom: 13px;
  background-color: #bebebe;
  margin: 0 auto;
  margin-bottom: 15px;
}
.text-process {
  font-size: 15px;
  margin-left: 9px;
  color: #000;
}
.suspendBtn {
  background-color: #969696;
  color: white;
  color: bold;
  border: none;
  outline: none;
  float: right;
  margin-right: 35px;
}
// .backBtn {
//   color: bold;
//   border: none;
//   outline: none;
//   float: left;
//   margin-left: 35px;
// }
.btnSubmit {
  width: 100%;
  margin: 0 auto;
  max-width: 860px;
  padding-left: 20px;
  padding-right: 20px;
  background-color: #a100ff;
  border-color: #a100ff;
}
@media screen and (min-width: 1024px) {
  .btnSubmit {
    max-width: 300px;
  }
}
.showBTN {
  text-align: center;
  padding-left: 20px;
  padding-right: 20px;
  margin-top: 10px;
  margin-bottom: 10px;
  border: none;
}
#__suspendBtn,
#__confirmSuspend,
#__cancelSuspend,
#__warningChooseClass,
#__warningChooseLang {
  background-color: #969696;
  color: #ffffff;
  border: none;
}
</style>
