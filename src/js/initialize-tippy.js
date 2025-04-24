import tippy, { hideAll } from "tippy.js";

const tippies = document.querySelectorAll(".gt-tooltip-trigger");

const instances = tippy(tippies, {
  allowHTML: true,
  interactive: true,
  hideOnClick: true,
  appendTo: "parent",
  duration: [200, 200],
  delay: [200, 200],
  theme: "light-border",
  animation: "shift-away-subtle",
  // animation: "scale",
  onShow(instance) {
    hideAll({ exclude: instance });
  },
  content(reference) {
    const id = reference.getAttribute("data-template-id");
    const template = document.querySelector(`[data-tooltip-id="${id}"]`);
    return template.innerHTML;
  },
});

// Set theme
instances.forEach((instance) => {
  const theme = instance.reference.getAttribute("data-tooltip-theme");
  const animation = instance.reference.getAttribute("data-tooltip-animation");
  const trigger = instance.reference.getAttribute("data-tooltip-trigger");
  // console.log('set theme: ', theme);
  instance.setProps({
    theme: theme,
    animation: animation,
    trigger: trigger,
  });
});
