import SocialPostGenerator from './components/SocialPostGenerator/SocialPanel.vue';

Statamic.booting(() => {
  Statamic.$components.register('social-post-generator', SocialPostGenerator);
})
