// You can add future interactivity here
console.log("SwiftCart homepage loaded.");
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("dropdownToggle");
  const menu = document.getElementById("dropdownMenu");

  toggle.addEventListener("click", (e) => {
    e.preventDefault();
    menu.classList.toggle("show");
  });

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (!toggle.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove("show");
    }
  });
});