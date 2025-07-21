<template>
  <div>
    <k-button-group
      layout="collapsed"
      class="k-language-selector"
      :aria-label="$t('junohamburg.language-selector.title')"
    >
      <k-button
        v-for="lang in languages"
        :key="lang.code"
        size="sm"
        variant="filled"
        v-bind="lang"
        :responsive="true"
        :text="lang.code"
        @click="change(lang)"
      />
      <k-button
        v-if="options.length > 0"
        :dropdown="true"
        :responsive="true"
        :title="$t('junohamburg.language-selector.settings')"
        icon="angle-down"
        size="sm"
        variant="filled"
        @click="$refs.options.toggle()"
      />
      <k-dropdown-content
        ref="options"
        alignX="end"
        :options="options"
        @action="remove($event)"
      />
    </k-button-group>

    <k-languages-dropdown
      :options="dropdown"
      :text="language.code"
      icon="translate"
      responsive="text"
      size="sm"
      variant="filled"
    />
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    dropdown: String,
    language: Object,
    languages: {
      type: Array,
      default: () => []
    },
    options: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    change(language) {
      this.$reload({
        query: {
          language: language.code
        }
      });
    },
    remove(code) {
      this.$panel.dialog.open(this.$panel.view.path + '/translation/' + code);
    }
  }
};
</script>

<style>
@container (max-width: 30rem) {
  .k-button-group.k-language-selector {
    display: none;
  }
}

@container (min-width: 30rem) {
  .k-language-selector + .k-languages-dropdown {
    display: none;
  }
}

/* Language Selector style */
.k-language-selector > .k-button,
.k-language-selector + .k-languages-dropdown > .k-button {
  text-transform: uppercase;
}

/* Same width buttons */
.k-language-selector > .k-button:not([data-dropdown='true']) {
  --button-padding: 0;
  min-width: 2rem;
}
</style>
