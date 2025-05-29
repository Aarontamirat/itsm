document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('faqSearch');
  const categorySelect = document.getElementById('faqCategory');
  const faqContainer = document.getElementById('faqContainer');

  function fetchFaqs() {
    const search = searchInput.value;
    const category = categorySelect.value;

    fetch(`fetch_faqs.php?search=${encodeURIComponent(search)}&category=${category}`)
      .then(res => res.text())
      .then(data => {
        faqContainer.innerHTML = data;
      });
  }

  searchInput.addEventListener('input', fetchFaqs);
  categorySelect.addEventListener('change', fetchFaqs);

  document.getElementById('exportCsv').addEventListener('click', () => {
    window.location.href = 'export_faqs_csv.php';
  });

  document.getElementById('exportPdf').addEventListener('click', () => {
    window.location.href = 'export_faqs_pdf.php';
  });
});
