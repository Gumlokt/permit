<template>
  <div class="card__text-field">
    <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': currentText || focus }" v-bind:for="field">{{ title }}</label>
    <input type="text" class="card__text-input" autocomplete="off"
      v-model="currentText"
      @input="getSuggestions"
      v-bind:name="field"
      v-bind:ref="field"
      v-bind:id="field"
      @focus="setFocus"
      @blur="unsetFocus"
      @keydown.down="down"
      @keydown.up="up"
      @keydown.enter.prevent="selectSuggestion(currentSuggestions[highlightIndex], highlightIndex)"
      @keydown.esc="openSuggestionsList = false"

    />
    <button class="card__button card__button_reset-input" @click.prevent="clearInput()"><span class="material-icons material-icons-outlined">clear</span></button>

    <ul class="suggestion-list" v-bind:class="{ 'suggestion-list_displayed': openSuggestionsList }" v-bind:id="field + '_list'">
      <li class="suggestion-list__item"
        v-for="(suggestion, index) in currentSuggestions"
        @click.prevent="selectSuggestion(suggestion, index)"
        @mousedown.prevent
        v-bind:class="{ highlighted : index === highlightIndex }"
        v-bind:id="field + index"
        v-bind:key="index"
      >
        {{ suggestion }}
      </li>
    </ul>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      typedText: null,
      focus: false,
      openSuggestionsList: false,
      highlightIndex: 0
    }
  },


  props: {
    field: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
  },


  methods: {
    ...mapMutations('permit', ['deletePermit']),
    ...mapMutations('permit', ['updatePermitSuggestions']),
    ...mapMutations('permit', ['updateNewPermit']),

    setFocus() {
      this.focus = true;
      this.currentText = this.typedText;
      this.currentSuggestions.length ? this.openSuggestionsList = true : this.openSuggestionsList = false;
    },

    unsetFocus() {
      this.openSuggestionsList = false;
      this.focus = false;
    },

    getSuggestions() {
      if (/\S+/.test(this.currentText)) {
        const res = fetch(`api/person/autocomplete`, {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            field: this.field,
            term: this.currentText
          }),
        })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          this.currentSuggestions = res;
          this.currentSuggestions.length ? this.openSuggestionsList = true : this.openSuggestionsList = false;
          this.highlightIndex = 0;

          return res; // res is an array of strings
        })
        .catch((err) => {
          console.log(err);
        });
      } else {
        this.clearInput();
      }
    },

    selectSuggestion(suggestion, index) {
      this.typedText = this.currentText;
      this.currentText = suggestion;  
      this.openSuggestionsList = false;
      this.highlightIndex = index;
    },

    up() {
      if (this.openSuggestionsList) {
        if (this.highlightIndex > 0) {
          this.highlightIndex--;
        }
      } else {
        this.openSuggestionsList = true;
      }

      this.scrollToSelectedElement(this.field + this.highlightIndex);
    },

    down() {
      if (this.openSuggestionsList) {
        if (this.highlightIndex < this.currentSuggestions.length - 1) {
          this.highlightIndex++;
        }
      } else {
        this.openSuggestionsList = true;
      }

      this.scrollToSelectedElement(this.field + this.highlightIndex);
    },

    scrollToSelectedElement(elementId) {
      let element = document.getElementById(elementId);

      if (element) {
        element.scrollIntoView(false);
      }
    },

    clearInput() {
      this.typedText = null;
      this.currentText = null;
      this.$refs[this.field].focus();
      this.currentSuggestions = [];
      this.highlightIndex = 0;
      this.openSuggestionsList = false;
    },
  },


  computed: {
    ...mapState({
      newPermit: state => state.permit.newPermit,
      permitSuggestions: state => state.permit.permitSuggestions,
    }),

    currentText: {
      get () { return this.newPermit[this.field]; },
      set (value) { this.updateNewPermit({ field: this.field, value: value }); }
    },

    currentSuggestions: {
      get () { return this.permitSuggestions[this.field]; },
      set (value) { this.updatePermitSuggestions({ field: this.field, value: value }); }
    },
  }
};
</script>

<style>
.suggestion-list {
  display: none;
  position: absolute;
  width: 100%;

  list-style: none;
  margin: 0;
  padding: 0;

  background-color: rgba(255, 255, 244, 0.96);
  border: 1px solid rgb(180, 198, 216);
  border-radius: 0 0 3px 3px;

  top: 45px;
  left: 0;
  line-height: 2em;

  z-index: 20;

  max-height: 132px;
  overflow-y: scroll;
}
  
.suggestion-list_displayed {
  display: block;
}
  
.suggestion-list li {
  cursor: pointer;
  scroll-snap-points-y: 20px;
}

.suggestion-list li:hover {
  color: #495057;
  background-color: #76B8FF;
}

.suggestion-list__item {
  padding: 0 3px;
}

.highlighted  {
  color: #fff;
  background-color: #76B8FF;
}
</style>
