(function () {
  'use strict';

  const LANGUAGE = {
    processing: 'Memproses...',
    lengthMenu: 'Tampilkan _MENU_ baris',
    search: '',
    searchPlaceholder: 'Cari data...',
    info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
    infoEmpty: 'Menampilkan 0 dari 0 data',
    infoFiltered: '(difilter dari _MAX_ data)',
    emptyTable: 'Belum ada data.',
    zeroRecords: 'Data tidak ditemukan.',
    paginate: {
      first: '<i class="mdi mdi-chevron-double-left" aria-hidden="true"></i>',
      previous: '<i class="mdi mdi-chevron-left" aria-hidden="true"></i>',
      next: '<i class="mdi mdi-chevron-right" aria-hidden="true"></i>',
      last: '<i class="mdi mdi-chevron-double-right" aria-hidden="true"></i>',
    },
    aria: {
      sortAscending: 'Klik untuk mengurutkan dari terkecil',
      sortDescending: 'Klik untuk mengurutkan dari terbesar',
    },
  };

  const EXPORT_LIBS = {
    excelHtml5: ['jszip.min.js'],
    pdfHtml5: ['pdfmake.min.js', 'vfs_fonts.js'],
  };

  function loadScript(url) {
    return new Promise(function (resolve, reject) {
      if (document.querySelector('script[src="' + url + '"]')) {
        resolve();
        return;
      }
      const tag = document.createElement('script');
      tag.src = url;
      tag.onload = resolve;
      tag.onerror = function () {
        reject(new Error('Gagal memuat berkas ekspor.'));
      };
      document.head.appendChild(tag);
    });
  }

  function exportLibUrl(file) {
    const tag = document.querySelector('script[src*="datatables.min.js"]');
    return new URL('export/' + file, tag.src).href;
  }

  function exportButton(name) {
    const base = window.DataTable.ext.buttons[name];
    const files = EXPORT_LIBS[name] || [];

    return {
      extend: name,
      available: function () {
        return true;
      },
      action: function (event, dt, node, config, callback) {
        const button = this;

        function run() {
          base.action.call(button, event, dt, node, config, callback);
        }

        if (!files.length) {
          run();
          return;
        }

        button.processing(true);
        files.reduce(function (chain, file) {
          return chain.then(function () {
            return loadScript(exportLibUrl(file));
          });
        }, Promise.resolve())
          .then(run)
          .catch(function (err) {
            window.alert(err.message);
          })
          .then(function () {
            button.processing(false);
          });
      },
    };
  }

  function exportBody(data) {
    if (typeof data !== 'string') return data;

    const holder = document.createElement('div');
    holder.innerHTML = data;

    const parts = [];
    holder.childNodes.forEach(function (node) {
      const text = node.textContent.replace(/\s+/g, ' ').trim();
      if (text) parts.push(text);
    });

    return parts.join(' ');
  }

  function buildExportLayout(table) {
    const columns = [];

    table.querySelectorAll('thead th').forEach(function (th, index) {
      if (!th.hasAttribute('data-no-export')) columns.push(index);
    });

    const title = table.getAttribute('data-export-title') || '';
    const shared = {
      title: title,
      messageTop: table.getAttribute('data-export-message') || undefined,
      filename: table.getAttribute('data-export-filename') || title || 'ekspor',
      exportOptions: { columns: columns, format: { body: exportBody } },
    };

    return {
      topStart: ['pageLength', {
        buttons: [
          Object.assign({}, shared, exportButton('csvHtml5'), { fieldSeparator: ';' }),
          Object.assign({}, shared, exportButton('excelHtml5')),
          Object.assign({}, shared, exportButton('pdfHtml5'), {
            orientation: 'landscape',
            pageSize: 'A4',
          }),
        ],
      }],
    };
  }

  function buildOptions(table) {
    const columnDefs = [];

    table.querySelectorAll('thead th').forEach(function (th, index) {
      const noSort = th.hasAttribute('data-no-sort');
      const noSearch = th.hasAttribute('data-no-search');
      if (noSort || noSearch) {
        columnDefs.push({
          targets: index,
          orderable: !noSort,
          searchable: !noSearch,
        });
      }
    });

    const emptyText = table.getAttribute('data-empty') || LANGUAGE.emptyTable;
    const pageLength = parseInt(table.getAttribute('data-page-length'), 10);

    const options = {
      pageLength: pageLength > 0 ? pageLength : 10,
      lengthMenu: [10, 25, 50, 100],
      autoWidth: false,
      columnDefs: columnDefs,
      language: Object.assign({}, LANGUAGE, { emptyTable: emptyText }),
    };

    if (table.hasAttribute('data-export')) {
      options.layout = buildExportLayout(table);
    }

    return options;
  }

  function initTables() {
    if (!window.DataTable) return;

    document.querySelectorAll('table[data-table]').forEach(function (table) {
      if (table.classList.contains('dataTable')) return;
      window.DataTable(table, buildOptions(table));
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTables);
  } else {
    initTables();
  }
})();
