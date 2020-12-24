<template>
  <div class="show-quiz-center">
    <!-- Display IMG Quiz -->
    <div class="show-quiz">
      <!-- Display tile -->
      <el-row>
        <el-col :span="24">
          <pre id="pre_title">{{ title }}</pre>
        </el-col>
      </el-row>
      <el-row v-if="image !== ''">
        <el-col :span="24">
          <!-- <el-image class="image_quiz" :src="image" /> -->
          <img :src="image" class="image_quiz" alt="">
        </el-col>
      </el-row>

      <!-- Display sentence -->
      <el-row>
        <el-col :span="24">
          <pre id="pre_sentence">{{ sentence }}</pre>
        </el-col>
      </el-row>
      <!-- Display Answer el-checkbox -->
      <div v-if="correctAnswer.length > 1">
        <el-checkbox-group
          v-for="(item, index) in options"
          :key="index"
          v-model="send.position_option"
          @change="optionChange(item.option_id)"
        >
          <el-row>
            <el-col :span="24" class="left">
              <el-checkbox
                :label="item.option_position"
                border
                :value="item.option_id"
                style="
                  width: 100%;
                  height: 100%;
                  align-items: center;
                  display: flex;
                "
                :disabled="disableCheckBtn"
              >
                <span class="break">{{ item.option_name }}</span>
              </el-checkbox>
            </el-col>
          </el-row>
        </el-checkbox-group>
      </div>
      <!-- Display Answer el-radio -->
      <div v-if="correctAnswer.length === 1">
        <el-radio-group
          v-for="(item, index) in options"
          :key="index"
          v-model="send.radio"
          class="anwser_radio"
          @change="optionChange(item.option_id)"
        >
          <el-row>
            <el-col :span="24" class="left">
              <el-radio
                :label="item.option_position"
                border
                :value="item.option_id"
                style="width: 100%; height: 100%; padding-bottom: 8px"
                :disabled="disableCheckBtn"
              >
                <span class="break">{{ item.option_name }}</span>
              </el-radio>
            </el-col>
          </el-row>
        </el-radio-group>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'QuizForQuizing',
  // Props to transfer data from father to child
  props: {
    // Title Quiz
    title: {
      type: String,
      default: '',
    },
    // Img Quiz
    image: {
      type: String,
      default: '',
    },
    // Sentence Quiz
    sentence: {
      type: String,
      default: '',
    },
    // Options Quiz
    options: {
      type: Array,
      required: true,
    },
    // Percent Complete Survey
    percent: {
      type: Number,
      default: 0,
    },
    // Disable checkbox and btn submit when respondent view quiz complete
    disableCheckBtn: {
      type: Boolean,
      default: false,
    },
    // Options select in quiz complete
    selectAnswerGet: {
      type: Array,
      required: true,
    },
    correctAnswer: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      // Array store options selected
      checkList: [],
      domain: process.env.MIX_STORE_IMAGE_URL,
      send: {
        id_option: [],
        position_option: [],
      },
    };
  },

  computed: {
    clearSelect() {
      return this.options;
    },
  },
  watch: {
    clearSelect() {
      // If respondent view quiz complete -> select options correct
      // If respondent doing quiz -> select options clean
      this.checkList = this.selectAnswerGet;
    },
  },
  methods: {
    // Method for tranfer options selected when checkbox change
    optionChange(option_id) {
      if (this.correctAnswer.length === 1) {
        this.send.position_option = [this.send.radio];
      }
      this.send.id_option.push(option_id);
      this.$emit('transferOptionSelect', this.send);
    },
  },
};
</script>

<style scoped lang="scss">
.show-quiz-center {
  text-align: center;
  height: 100%;
  flex: 1;
}
.space {
  padding: 5px;
}

.left {
  text-align: left;
  margin-top: 5px;
  margin-bottom: 5px;
}

.break {
  white-space: pre-line;
}
.process {
  text-align: center;
  padding-top: 13px;
  padding-bottom: 13px;
  background-color: #bebebe;
}
.show-quiz {
  margin: 0 auto;
  max-width: 900px;
  padding-left: 20px;
  padding-right: 20px;
}
.anwser_radio {
  display: flex;
  flex-direction: column;
}
.image_quiz {
  object-fit: contain;
  // width: 100%;
  // max-width: 900px;
  max-width: 100%;
  // height: 100px;
}
</style>
