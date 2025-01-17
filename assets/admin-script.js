document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("js-file-input");
  const fileList = document.getElementById("js-file-list");

  fileInput.addEventListener("change", function () {
    // Vide la liste des fichiers affichés
    fileList.innerHTML = "";

    Array.from(fileInput.files).forEach((file, index) => {
      const listItem = document.createElement("li");
      listItem.textContent = file.name;

      const removeButton = document.createElement("button");
      removeButton.textContent = "Remove";
      removeButton.type = "button";
      removeButton.className = "button button-secondary";

      removeButton.addEventListener("click", function () {
        removeFile(index);
      });

      listItem.appendChild(removeButton);
      fileList.appendChild(listItem);
    });
  });

  function removeFile(index) {
    const dataTransfer = new DataTransfer();

    Array.from(fileInput.files).forEach((file, i) => {
      if (i !== index) {
        dataTransfer.items.add(file);
      }
    });

    fileInput.files = dataTransfer.files;

    // Rafraîchit la liste des fichiers affichés
    fileInput.dispatchEvent(new Event("change"));
  }
});