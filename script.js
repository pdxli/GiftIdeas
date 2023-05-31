// Dodajemy listener do formularza, który uruchamia się po zdarzeniu "submit"
document.querySelector("form").addEventListener("submit", function (event) {
  // Zapobiegamy domyślnemu działaniu formularza, które przeładowałoby stronę
  event.preventDefault();

  // Tworzymy obiekt FormData, który zbiera dane z formularza
  const formData = new FormData(this);

  // Wykonujemy zapytanie fetch do skryptu PHP z danymi z formularza
  fetch("generate_gift.php", {
    method: "POST",
    body: formData,
  })
    // Po otrzymaniu odpowiedzi zamieniamy ją na format JSON
    .then((response) => response.json())
    // Po konwersji na format JSON
    .then((data) => {
      // Szukamy elementu na stronie do wyświetlenia pomysłów na prezenty
      const giftIdeas = document.querySelector("#gift-ideas");

      // Rozpoczynamy tworzenie listy pomysłów na prezenty
      let giftIdeasList = "<h1>Pomysły na prezenty:</h1><table>";

      // Przechodzimy przez każdy pomysł na prezent
      data.forEach(function (idea) {
        // Dodajemy pomysł do listy
        giftIdeasList += "<tr><td>" + idea + "</td></tr>";
      });

      // Kończymy tworzenie listy
      giftIdeasList += "</table>";

      // Wyświetlamy listę pomysłów na prezenty na stronie
      giftIdeas.innerHTML = giftIdeasList;
    })
    // W przypadku błędu wyświetlamy go w konsoli
    .catch((error) => {
      console.error("Error:", error);
    });
});
