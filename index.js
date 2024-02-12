panel.plugin('junohamburg/language-selector', {
  use: [
    function (Vue) {
      const original = Vue.component('k-languages-dropdown');
      let hasSubscriber = false;

      Vue.component('k-languages-dropdown', {
        created() {
          // Subscribe to save action once to update the languages
          if (hasSubscriber) return;
          hasSubscriber = true;

          this.$store.subscribeAction({
            after: (action, state) => {
              if (action.type === 'content/save') {
                // Update languages manually, since no api fetch is triggered
                let updatedLanguage = {};
                let updatedLanguageIndex = 0;

                for (const language of this.languages) {
                  if (language.code === this.language.code) {
                    updatedLanguageIndex = this.languages.indexOf(language);

                    // Create a clone of the language object
                    updatedLanguage = structuredClone(language);
                    updatedLanguage.hasTranslation = true;
                  }
                }

                // Update reactive data
                this.$set(
                  this.languages,
                  updatedLanguageIndex,
                  updatedLanguage
                );
              }
            }
          });
        },

        data() {
          return {
            ...original.options.data.call(this),
            prefix: 'junohamburg.language-selector'
          };
        },

        computed: {
          ...original.options.computed,
          sortedLanguages() {
            return this.languages.sort((a, b) => {
              if (a.default) return -1;
              if (b.default) return 1;
              return 0;
            });
          },
          dropdownVisible() {
            if (panel.config.languageSelector?.allowDelete === false) {
              return false;
            }

            // Check for existing translations
            let existingTranslations = 0;

            for (const language of Object.values(this.languages)) {
              if (language.hasTranslation) existingTranslations++;
            }

            return existingTranslations > 1;
          },
          dropdownOptions() {
            const options = [];

            // Create dropdown options
            for (const language of Object.values(this.languages)) {
              if (language.default) continue;

              const option = {
                text: this.$t(`${this.prefix}.delete`, {
                  language: language.name
                }),
                icon: 'trash',
                current: false,
                disabled: !language.hasTranslation,
                click: () => {
                  // Use panel.view.component to get the current view type
                  const type = panel.view.component.match(/k-([a-z]+)-view/)[1];

                  // Get dialog text for site, page, file, user or account
                  const text = this.$t(
                    `${this.prefix}.${type}.delete.confirm`,
                    {
                      language: language.name,
                      title: panel.view.title
                    }
                  );

                  // Open remove dialog
                  panel.dialog.open({
                    component: 'k-remove-dialog',
                    props: {
                      text: text
                    },
                    on: {
                      submit: () => {
                        // Delete translation
                        const model = this.$store.state.content.current;
                        panel.api.delete('translation/delete/' + model, {
                          language: language.code
                        });

                        // Close dialog
                        this.$panel.dialog.close();

                        // Reload view
                        this.change(this.language);
                      }
                    }
                  });
                }
              };

              options.push(option);
            }

            return options;
          }
        },

        methods: {
          ...original.options.methods,
          theme(language) {
            let theme = false;

            if (language.code === this.language.code) {
              theme = 'dark';
            } else if (language.hasTranslation === false) {
              theme = 'empty';
            }

            return theme;
          },
          title(language) {
            let title = language.name;

            if (language.hasTranslation === false) {
              title = this.$t(`${this.prefix}.empty`, {
                language: language.name
              });
            }

            return title;
          }
        },

        template: `
          <div v-if="languages.length > 1">
            <k-button-group
              layout="collapsed"
              class="k-language-selector"
              :aria-label="$t(prefix + '.title')"
            >
              <k-button
                v-for="lang in sortedLanguages"
                :key="lang.code"
                size="sm"
                variant="filled"
                :responsive="true"
                :current="lang.code === language.code"
                :text="lang.code"
                :title="title(lang)"
                :theme="theme(lang)"
                @click="change(lang)"
              />

              <k-button
                v-if="dropdownVisible"
                :dropdown="true"
                :responsive="true"
                :title="$t(prefix + '.settings')"
                icon="angle-down"
                size="sm"
                variant="filled"
                @click="$refs.options.toggle()"
              />
              <k-dropdown-content ref="options" alignX="end" :options="dropdownOptions" />
            </k-button-group>

            <div class="k-languages-dropdown">
              <k-button
                :dropdown="true"
                :text="code"
                icon="translate"
                responsive="text"
                size="sm"
                variant="filled"
                @click="$refs.languages.toggle()"
              />
              <k-dropdown-content ref="languages" :options="options" />
            </div>
          </div>
        `
      });
    }
  ]
});
