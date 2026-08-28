/* =========================================================
   SIMPM — app.js
   Interaktivitas: splash screen, toast, animasi angka statistik,
   filter/pencarian tabel live, sort kolom tabel, toggle checklist
   via AJAX, dan tab mesin pada halaman performa.
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
  initSplash();
  initToasts();
  animateStatNumbers();
  animateProgressBars();
  initTableSearch();
  initSortableTables();
  initChecklistToggle();
  initMachineTabs();
  initSidebarToggle();
});

/* ---------- Splash screen ---------- */
function initSplash() {
  const splash = document.getElementById('splashScreen');
  if (!splash) return;
  setTimeout(function () {
    splash.classList.add('fade-out');
    setTimeout(function () { splash.style.display = 'none'; }, 420);
  }, 1100);
}

/* ---------- Toast notifications ---------- */
function ensureToastWrap() {
  let wrap = document.querySelector('.toast-wrap');
  if (!wrap) {
    wrap = document.createElement('div');
    wrap.className = 'toast-wrap';
    document.body.appendChild(wrap);
  }
  return wrap;
}

function showToast(message, type) {
  const wrap = ensureToastWrap();
  const toast = document.createElement('div');
  toast.className = 'toast ' + (type || '');
  toast.textContent = message;
  wrap.appendChild(toast);
  setTimeout(function () {
    toast.classList.add('hide');
    setTimeout(function () { toast.remove(); }, 260);
  }, 3200);
}
window.showToast = showToast;

// Ambil pesan sukses dari session flash (dirender server sebagai data-attribute)
function initToasts() {
  const flash = document.getElementById('flashData');
  if (!flash) return;
  const success = flash.dataset.success;
  const error = flash.dataset.error;
  if (success) showToast(success, 'success');
  if (error) showToast(error, 'error');
}

/* ---------- Animasi angka statistik (count-up) ---------- */
function animateStatNumbers() {
  document.querySelectorAll('.stat-num[data-count]').forEach(function (el) {
    const target = parseFloat(el.dataset.count);
    const decimals = el.dataset.count.includes('.') ? el.dataset.count.split('.')[1].length : 0;
    const unitEl = el.querySelector('.unit');
    const unitHTML = unitEl ? unitEl.outerHTML : '';
    let current = 0;
    const duration = 700;
    const steps = 30;
    const increment = target / steps;
    const stepTime = duration / steps;

    const timer = setInterval(function () {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.innerHTML = current.toFixed(decimals) + unitHTML;
    }, stepTime);
  });
}

/* ---------- Progress bar animasi (trigger reflow lalu set width) ---------- */
function animateProgressBars() {
  document.querySelectorAll('.progress-fill[data-width]').forEach(function (el) {
    const w = el.dataset.width;
    requestAnimationFrame(function () {
      setTimeout(function () { el.style.width = w + '%'; }, 80);
    });
  });
}

/* ---------- Filter/pencarian tabel live (client-side) ---------- */
function initTableSearch() {
  document.querySelectorAll('[data-table-search]').forEach(function (input) {
    const tableId = input.dataset.tableSearch;
    const table = document.getElementById(tableId);
    if (!table) return;
    input.addEventListener('input', function () {
      const q = input.value.toLowerCase().trim();
      table.querySelectorAll('tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  });
}

/* ---------- Sort tabel dengan klik header ---------- */
function initSortableTables() {
  document.querySelectorAll('table[data-sortable] thead th').forEach(function (th, idx) {
    th.addEventListener('click', function () {
      const table = th.closest('table');
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));
      const asc = th.dataset.sortDir !== 'asc';

      table.querySelectorAll('thead th').forEach(h => delete h.dataset.sortDir);
      th.dataset.sortDir = asc ? 'asc' : 'desc';

      rows.sort(function (a, b) {
        const av = a.children[idx].textContent.trim();
        const bv = b.children[idx].textContent.trim();
        const an = parseFloat(av.replace(/[^0-9.-]/g, ''));
        const bn = parseFloat(bv.replace(/[^0-9.-]/g, ''));
        let cmp;
        if (!isNaN(an) && !isNaN(bn) && av.match(/[0-9]/)) {
          cmp = an - bn;
        } else {
          cmp = av.localeCompare(bv);
        }
        return asc ? cmp : -cmp;
      });

      rows.forEach(r => tbody.appendChild(r));
    });
  });
}

/* ---------- Checklist toggle via AJAX (Teknisi: Detail Jadwal PM) ---------- */
function initChecklistToggle() {
  document.querySelectorAll('.checklist li[data-item-id]').forEach(function (li) {
    li.addEventListener('click', function () {
      if (li.classList.contains('saving')) return;
      li.classList.add('saving');

      const itemId = li.dataset.itemId;
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      if (!metaTag) return;
      const token = metaTag.content;

      fetch('/teknisi/checklist/' + itemId + '/toggle', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
      })
        .then(res => res.json())
        .then(function (data) {
          li.classList.remove('saving');
          li.classList.toggle('done', data.is_done);
          updateChecklistProgress(data.progres);
        })
        .catch(function () {
          li.classList.remove('saving');
          showToast('Gagal menyimpan checklist, coba lagi.', 'error');
        });
    });
  });
}

function updateChecklistProgress(progres) {
  const bar = document.querySelector('[data-checklist-progress]');
  const label = document.querySelector('[data-checklist-progress-label]');
  if (bar) bar.style.width = progres.persen + '%';
  if (label) label.textContent = progres.selesai + ' / ' + progres.total + ' item selesai';
}

/* ---------- Tab mesin (halaman Performa Manajer / Detail Mesin) ---------- */
function initMachineTabs() {
  document.querySelectorAll('.machine-tab-row').forEach(function (row) {
    row.querySelectorAll('.mtab').forEach(function (btn) {
      btn.addEventListener('click', function () {
        row.querySelectorAll('.mtab').forEach(b => b.classList.remove('on'));
        btn.classList.add('on');

        const mesinId = btn.dataset.mesinId || '';
        const url = new URL(window.location.href);
        if (mesinId) {
          url.searchParams.set('mesin_id', mesinId);
        } else {
          url.searchParams.delete('mesin_id');
        }
        window.location.href = url.toString();
      });
    });
  });
}

/* ---------- Sidebar toggle (mobile) ---------- */
function initSidebarToggle() {
  const btn = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  if (!btn || !sidebar) return;
  btn.addEventListener('click', function () {
    sidebar.classList.toggle('open');
  });
}

/* =========================================================
   Chart.js helpers — dipanggil dari view dengan data JSON
   yang di-render server (lihat folder resources/views)
   ========================================================= */
function renderLineChart(canvasId, labels, datasets, opts) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return null;
  return new Chart(ctx, {
    type: 'line',
    data: { labels: labels, datasets: datasets },
    options: Object.assign({
      responsive: true,
      plugins: { legend: { display: datasets.length > 1, position: 'bottom' } },
      scales: { y: { beginAtZero: false } },
    }, opts || {}),
  });
}

function renderBarChart(canvasId, labels, datasets, opts) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return null;
  return new Chart(ctx, {
    type: 'bar',
    data: { labels: labels, datasets: datasets },
    options: Object.assign({
      responsive: true,
      plugins: { legend: { display: datasets.length > 1, position: 'bottom' } },
      scales: { y: { beginAtZero: true } },
    }, opts || {}),
  });
}

function renderStackedBarChart(canvasId, labels, datasets, opts) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return null;
  return new Chart(ctx, {
    type: 'bar',
    data: { labels: labels, datasets: datasets },
    options: Object.assign({
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
      scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
    }, opts || {}),
  });
}

window.renderLineChart = renderLineChart;
window.renderBarChart = renderBarChart;
window.renderStackedBarChart = renderStackedBarChart;

/* =========================================================
   Ekspor PDF & Excel (halaman Laporan Supervisor / Manajer)
   - PDF  : html2pdf.js menangkap elemen #laporanContent jadi A4
   - Excel: SheetJS menulis beberapa sheet dari data JSON server
   ========================================================= */
function exportLaporanPdf(elementId, filename) {
  const el = document.getElementById(elementId);
  if (!el) return;
  if (typeof html2pdf === 'undefined') {
    showToast('Library PDF belum termuat.', 'error');
    return;
  }
  showToast('Menyiapkan berkas PDF...', 'success');
  html2pdf().set({
    margin: [10, 10, 12, 10],
    filename: filename,
    image: { type: 'jpeg', quality: 0.95 },
    html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['css', 'legacy'] },
  }).from(el).save().catch(function () {
    showToast('Gagal membuat PDF.', 'error');
  });
}

function exportSheetsToExcel(filename, sheets) {
  if (typeof XLSX === 'undefined') {
    showToast('Library Excel belum termuat.', 'error');
    return;
  }
  try {
    const wb = XLSX.utils.book_new();
    sheets.forEach(function (sheet) {
      const ws = XLSX.utils.aoa_to_sheet(sheet.rows);
      const widths = [];
      sheet.rows.forEach(function (row) {
        row.forEach(function (cell, c) {
          const len = String(cell == null ? '' : cell).length + 4;
          if (!widths[c] || len > widths[c].wch) widths[c] = { wch: Math.min(len, 60) };
        });
      });
      ws['!cols'] = widths;
      XLSX.utils.book_append_sheet(wb, ws, sheet.name.substring(0, 31));
    });
    XLSX.writeFile(wb, filename);
    showToast('Excel berhasil diunduh.', 'success');
  } catch (e) {
    showToast('Gagal membuat Excel.', 'error');
  }
}

window.exportLaporanPdf = exportLaporanPdf;
window.exportSheetsToExcel = exportSheetsToExcel;
