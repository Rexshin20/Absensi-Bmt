/* Materio port — vanilla JS replacement for the MUI interactive behaviour */

(function () {
  'use strict';

  // ---------------------------------------------------------------------------
  // Theme (light / dark)
  // ---------------------------------------------------------------------------
  var THEME_KEY = 'materio-theme';

  function isDark() {
    return document.documentElement.getAttribute('data-theme') === 'dark';
  }

  function applyTheme(theme, persist) {
    document.documentElement.setAttribute('data-theme', theme);

    if (persist) {
      try {
        localStorage.setItem(THEME_KEY, theme);
      } catch (error) {}
    }

    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
      var lightIcon = button.querySelector('.theme-icon-light');
      var darkIcon = button.querySelector('.theme-icon-dark');
      var dark = theme === 'dark';
      if (lightIcon) lightIcon.classList.toggle('d-none', dark);
      if (darkIcon) darkIcon.classList.toggle('d-none', !dark);
    });

    var chartElement = document.getElementById('weekly-overview-chart');
    if (window.MaterioCharts && window.MaterioCharts.weekly && chartElement && chartElement.__materioChart) {
      window.MaterioCharts.weekly(chartElement);
    }
  }

  document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      applyTheme(isDark() ? 'light' : 'dark', true);
    });
  });

  applyTheme(isDark() ? 'dark' : 'light', false);

  // ---------------------------------------------------------------------------
  // Dropdown menus (user menu, notifications)
  // ---------------------------------------------------------------------------
  function closeAllMenus(except) {
    document.querySelectorAll('.menu-popover.open').forEach(function (menu) {
      if (menu !== except) {
        menu.classList.remove('open');
      }
    });
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('[data-menu-trigger]');

    if (trigger) {
      event.preventDefault();
      event.stopPropagation();

      var menu = document.getElementById(trigger.getAttribute('data-menu-trigger'));
      if (!menu) {
        return;
      }

      var willOpen = !menu.classList.contains('open');
      closeAllMenus(menu);
      menu.classList.toggle('open', willOpen);
      return;
    }

    if (!event.target.closest('.menu-popover')) {
      closeAllMenus(null);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeAllMenus(null);
    }
  });

  // ---------------------------------------------------------------------------
  // Sidebar drawer (below 1200px, MUI down('lg'))
  // ---------------------------------------------------------------------------
  function setNavOpen(open) {
    var nav = document.querySelector('.layout-vertical-nav');
    var backdrop = document.querySelector('.nav-backdrop');

    if (!nav) {
      return;
    }

    nav.classList.toggle('nav-open', open);
    if (backdrop) {
      backdrop.classList.toggle('show', open);
    }
  }

  document.querySelectorAll('[data-nav-toggle]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      var nav = document.querySelector('.layout-vertical-nav');
      setNavOpen(!(nav && nav.classList.contains('nav-open')));
    });
  });

  document.querySelectorAll('[data-nav-close]').forEach(function (element) {
    element.addEventListener('click', function () {
      setNavOpen(false);
    });
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 1200) {
      setNavOpen(false);
    }
  });

  // ---------------------------------------------------------------------------
  // Tabs (account settings / navigation cards)
  // ---------------------------------------------------------------------------
  document.querySelectorAll('[data-tabs]').forEach(function (group) {
    var tabs = group.querySelectorAll('.tab');
    var card = group.closest('.card') || document;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        tabs.forEach(function (item) {
          item.classList.remove('active');
          item.setAttribute('aria-selected', 'false');
        });

        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');

        var target = tab.getAttribute('data-tab');

        card.querySelectorAll('.tab-panel').forEach(function (panel) {
          panel.classList.toggle('active', panel.getAttribute('data-panel') === target);
        });
      });
    });
  });

  // ---------------------------------------------------------------------------
  // Collapse (collapsible card / table rows)
  // ---------------------------------------------------------------------------
  document.querySelectorAll('[data-collapse-toggle]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var targetId = trigger.getAttribute('data-collapse-toggle');
      var target = document.getElementById(targetId);
      if (!target) {
        return;
      }

      var expanded = target.classList.toggle('show');

      document.querySelectorAll('[data-collapse-toggle="' + targetId + '"]').forEach(function (toggle) {
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');

        var openIcon = toggle.querySelector('.chevron-up');
        var closedIcon = toggle.querySelector('.chevron-down');

        if (openIcon && closedIcon) {
          openIcon.classList.toggle('d-none', !expanded);
          closedIcon.classList.toggle('d-none', expanded);
        }
      });
    });
  });

  // ---------------------------------------------------------------------------
  // Password visibility toggles
  // ---------------------------------------------------------------------------
  document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
    button.addEventListener('mousedown', function (event) {
      event.preventDefault();
    });

    button.addEventListener('click', function () {
      var input = document.getElementById(button.getAttribute('data-toggle-password'));
      if (!input) {
        return;
      }

      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';

      button.querySelector('.eye-on').classList.toggle('d-none', !show);
      button.querySelector('.eye-off').classList.toggle('d-none', show);
      button.setAttribute('aria-label', show ? 'hide password' : 'show password');
    });
  });

  // ---------------------------------------------------------------------------
  // Dismissible alerts
  // ---------------------------------------------------------------------------
  document.querySelectorAll('[data-alert-close]').forEach(function (button) {
    button.addEventListener('click', function () {
      var alert = button.closest('.alert');
      if (alert) {
        alert.remove();
      }
    });
  });

  // ---------------------------------------------------------------------------
  // Account settings — avatar preview / reset
  // ---------------------------------------------------------------------------
  var avatarInput = document.getElementById('account-settings-upload-image');
  var avatarImage = document.getElementById('account-settings-avatar');

  if (avatarInput && avatarImage) {
    avatarInput.addEventListener('change', function () {
      var file = avatarInput.files && avatarInput.files[0];
      if (!file) {
        return;
      }

      var reader = new FileReader();
      reader.onload = function () {
        avatarImage.src = reader.result;
      };
      reader.readAsDataURL(file);
    });
  }

  document.querySelectorAll('[data-avatar-reset]').forEach(function (button) {
    button.addEventListener('click', function () {
      if (avatarImage) {
        avatarImage.src = button.getAttribute('data-avatar-reset');
      }
      if (avatarInput) {
        avatarInput.value = '';
      }
    });
  });

  // ---------------------------------------------------------------------------
  // Sticky table pagination (mirrors MUI TablePagination demo)
  // ---------------------------------------------------------------------------
  document.querySelectorAll('[data-table-pagination]').forEach(function (table) {
    var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
    var scope = table.closest('.card-content') || table.parentElement;
    var sizeSelect = scope.querySelector('[data-page-size]');
    var nextButton = scope.querySelector('[data-page-next]');
    var prevButton = scope.querySelector('[data-page-prev]');
    var label = scope.querySelector('[data-page-label]');
    var page = 0;

    function size() {
      return sizeSelect ? parseInt(sizeSelect.value, 10) : 10;
    }

    function render() {
      var perPage = size();
      var pages = Math.max(1, Math.ceil(rows.length / perPage));

      if (page >= pages) {
        page = pages - 1;
      }

      rows.forEach(function (row, index) {
        row.style.display = index >= page * perPage && index < (page + 1) * perPage ? '' : 'none';
      });

      if (label) {
        var from = rows.length === 0 ? 0 : page * perPage + 1;
        var to = Math.min(rows.length, (page + 1) * perPage);
        label.textContent = from + '–' + to + ' of ' + rows.length;
      }

      if (prevButton) {
        prevButton.disabled = page === 0;
      }
      if (nextButton) {
        nextButton.disabled = page >= pages - 1;
      }
    }

    if (sizeSelect) {
      sizeSelect.addEventListener('change', function () {
        page = 0;
        render();
      });
    }

    if (nextButton) {
      nextButton.addEventListener('click', function () {
        page += 1;
        render();
      });
    }

    if (prevButton) {
      prevButton.addEventListener('click', function () {
        page -= 1;
        render();
      });
    }

    render();
  });

  // ---------------------------------------------------------------------------
  // Scroll to top + reading progress
  // ---------------------------------------------------------------------------
  var scrollTop = document.querySelector('.scroll-top');

  if (scrollTop) {
    scrollTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  window.addEventListener(
    'scroll',
    function () {
      if (scrollTop) {
        scrollTop.classList.toggle('show', window.scrollY > 400);
      }
    },
    { passive: true }
  );

  // ---------------------------------------------------------------------------
  // Left navigation active link (in addition to server-side active state)
  // ---------------------------------------------------------------------------
  document.querySelectorAll('.nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth < 1200) {
        setNavOpen(false);
      }
    });
  });

  // ---------------------------------------------------------------------------
  // Charts (ApexCharts loaded from CDN)
  // ---------------------------------------------------------------------------
  window.MaterioCharts = {
    weekly: function (element) {
      if (!element || typeof ApexCharts === 'undefined') {
        return;
      }

      if (element.__materioChart) {
        element.__materioChart.destroy();
        element.__materioChart = null;
      }

      var dark = isDark();
      var surface = dark ? '#312d4b' : '#fff';
      var track = dark ? '#3d3759' : '#f4f5fa';
      var labelColor = dark ? 'rgba(231, 227, 252, 0.68)' : 'rgba(58, 53, 65, 0.68)';
      var gridColor = dark ? 'rgba(231, 227, 252, 0.12)' : 'rgba(58, 53, 65, 0.12)';

      var options = {
        chart: {
          parentHeightOffset: 0,
          toolbar: { show: false },
          animations: { enabled: true }
        },
        plotOptions: {
          bar: {
            borderRadius: 9,
            distributed: true,
            columnWidth: '40%',
            endingShape: 'rounded',
            startingShape: 'rounded'
          }
        },
        stroke: { width: 2, colors: [surface] },
        legend: { show: false },
        grid: {
          strokeDashArray: 7,
          borderColor: gridColor,
          padding: { top: -1, right: 0, left: -12, bottom: 5 }
        },
        dataLabels: { enabled: false },
        colors: [track, track, track, '#9155fd', track, track, track],
        states: {
          hover: { filter: { type: 'none' } },
          active: { filter: { type: 'none' } }
        },
        xaxis: {
          categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
          tickPlacement: 'on',
          labels: { show: false },
          axisTicks: { show: false },
          axisBorder: { show: false }
        },
        yaxis: {
          show: true,
          tickAmount: 4,
          labels: {
            offsetX: -17,
            style: { colors: labelColor, fontSize: '12px' },
            formatter: function (value) {
              return (value > 999 ? (value / 1000).toFixed(0) : value) + 'k';
            }
          }
        }
      };

      options.chart.type = 'bar';
      options.chart.height = 205;
      options.series = [{ data: [37, 57, 45, 75, 57, 40, 65] }];

      var chart = new ApexCharts(element, options);

      element.__materioChart = chart;
      chart.render();
    }
  };
})();
