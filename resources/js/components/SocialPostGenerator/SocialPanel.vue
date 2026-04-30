<template>
  <div class="flex flex-row justify-center">
    <div class="w-1/4 bg-white rounded-lg shadow-lg p-8 mr-4 max-h-screen overflow-y-auto">
      <div class="mb-4">
        <div class="prose" id="page_preview"></div>
      </div>
    </div>
    <div class="w-3/4 bg-white rounded-lg shadow-lg p-8">
      <div class="flex items-center social-buttons">
        <button v-for="social in socials" :key="social.id" @click="generatePost(social)"
          class="btn-primary" :disabled="buttonsDisabled">
          {{ social.name }}
        </button>
      </div>
      <div id="cards" class="mt-4 overflow-y-auto max-h-screen">
        <div v-for="card in cards" :key="card.id" class="bg-white rounded-lg border border-gray-200 px-8 py-6 my-4">
          <div class="flex items-center justify-between">
            <h2>{{ card.social.name }}</h2>
            <button @click="copyTextToClipboard(card.rawPost)" class="btn-round flex items-center justify-center">
              <svg-icon name="micro/clipboard-copy" class="w-4 h-4" />
            </button>
          </div>
          <div class="prose max-w-full mt-4" v-html="card.post"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Readability } from '@mozilla/readability';
import TurndownService from 'turndown';
import { SSE } from 'sse.js';
import markdownit from "markdown-it";

export default {
    name: 'SocialPostGeneratorPanel',
    props: {
        socials: {
            type: Array,
            required: true
        },
        default: () => []
    },
    data() {
        return {
            buttonsDisabled: false,
            input_text: '',
            cards: []
        }
    },
    methods: {
        toggleButtons(value) {
            this.buttonsDisabled = !value;
        },
        getCleanedDOM(htmlText) {
            const temp = document.createElement('div');
            temp.innerHTML = htmlText;
            const raw_html = temp.childNodes[0].nodeValue;
            const parser = new DOMParser();
            const doc = parser.parseFromString(raw_html, 'text/html');
            doc.querySelectorAll('script').forEach(script => script.remove());

            return doc;
        },
        async copyTextToClipboard(text) {
            if (navigator.clipboard) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.$toast.success('Copied to clipboard');
                } catch (err) {
                    console.error('Failed to copy: ', err);
                    this.$toast.error('Failed to copy to clipboard');
                }
            } else {
                const textArea = document.createElement("textarea");

                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.$toast.success('Copied to clipboard');
                } catch (err) {
                    console.error('Failed to copy: ', err);
                    this.$toast.error('Failed to copy to clipboard');
                }
                document.body.removeChild(textArea);
            }
        },
        async generatePost(social) {
            this.toggleButtons(false);

            const currentCard = {
              id: this.cards.length + 1,
              social: social,
              rawPost: '',
              post: '',
              status: 'pending'
            }
            this.cards.unshift(currentCard);

            const llmRequest = new SSE(window.location.href, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.getElementById('csrf_token').innerText
                },
                withCredentials: true,
                payload: JSON.stringify({
                    page_content: this.input_text,
                    social: social.id
                }),
                start: false
            })

            llmRequest.addEventListener('message', (event) => {
                if (!event.data) return
                const data = JSON.parse(event.data);
                if(data.type === 'chunk') {
                    currentCard.status = 'processing';
                    currentCard.rawPost += data.delta;
                    currentCard.post = markdownit().render(currentCard.rawPost);
                } else if(data.type === 'end') {
                    currentCard.status = 'done';
                    this.toggleButtons(true);
                }
            })

            llmRequest.addEventListener('abort', (ev) => {
                currentCard.status = 'aborted';
                this.toggleButtons(true);
            })

            llmRequest.addEventListener('error', (ev) => {
                currentCard.status = 'error';
                this.toggleButtons(true);
            })

            llmRequest.addEventListener('readystatechange', (event) => {
              // READYSTATE 2 = CONNECTION CLOSED
              if(event.readyState === 2 && currentCard.status === 'processing') {
                currentCard.status = 'done';
                this.toggleButtons(true);
              }
            })

            llmRequest.stream();
        }
    },
    mounted() {
        const raw_html_page = document.getElementById('raw_html_page').innerText;

        const doc = this.getCleanedDOM(raw_html_page);
        const article = new Readability(doc).parse();

        let preview = "<h1>" + article.title + "</h1>\n\n"
        preview += article.content;
        document.getElementById('page_preview').innerHTML = preview;

        let result = "# " + article.title + "\n\n"
        const turndown = new TurndownService();
        result += turndown.turndown(article.content);
        this.input_text = result;
    }
}
</script>
