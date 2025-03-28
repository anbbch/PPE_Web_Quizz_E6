// document.querySelectorAll(".subject-button").forEach(button => {
button.addEventListener("click", () => {
  const themeId = button.dataset.subject;
  window.location.href = `Quizz.php?theme=${themeId}`;
});
document.addEventListener("DOMContentLoaded", function () {
  const menuButton = document.querySelector(".menu-button");
  const menuDropdown = document.querySelector(".menu-dropdown");

  menuButton.addEventListener("click", function (event) {
    event.stopPropagation(); // Empêche la propagation pour éviter de fermer immédiatement
    menuDropdown.style.display =
      menuDropdown.style.display === "block" ? "none" : "block";
  });

  document.addEventListener("click", function (event) {
    if (
      !menuButton.contains(event.target) &&
      !menuDropdown.contains(event.target)
    ) {
      menuDropdown.style.display = "none";
    }
  });
});

function openPopup() {
  document.getElementById("theme-popup").style.display = "block";
  document.getElementById("overlay").style.display = "block";
}
function closePopup() {
  document.getElementById("theme-popup").style.display = "none";
  document.getElementById("overlay").style.display = "none";
}
function confirmDelete() {
  return confirm("Voulez-vous vraiment supprimer ce thème ?");
}

document.addEventListener("DOMContentLoaded", function () {
  const menuButton = document.querySelector(".menu-button");
  const menuDropdown = document.querySelector(".menu-dropdown");

  menuButton.addEventListener("click", function (event) {
    event.stopPropagation(); // Empêche la propagation pour éviter de fermer immédiatement
    menuDropdown.style.display =
      menuDropdown.style.display === "block" ? "none" : "block";
  });

  document.addEventListener("click", function (event) {
    if (
      !menuButton.contains(event.target) &&
      !menuDropdown.contains(event.target)
    ) {
      menuDropdown.style.display = "none";
    }
  });
});
