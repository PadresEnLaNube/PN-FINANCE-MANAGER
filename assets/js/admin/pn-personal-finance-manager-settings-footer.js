(function () {
  'use strict';
  var saveBtn = document.getElementById('pn-personal-finance-manager-settings-save');
  var exportBtn = document.getElementById('pn-personal-finance-manager-settings-export');
  var importBtn = document.getElementById('pn-personal-finance-manager-settings-import');
  var fileInput = document.getElementById('pn-personal-finance-manager-settings-import-file');
  if (!saveBtn) return;

  var menuToggle = document.getElementById('wp-admin-bar-menu-toggle');
  var footer = document.getElementById('pn-personal-finance-manager-settings-footer');
  if (menuToggle && footer) {
    menuToggle.addEventListener('click', function () {
      setTimeout(function () {
        footer.style.display = document.body.classList.contains('wp-responsive-open') ? 'none' : '';
      }, 0);
    });
  }

  saveBtn.addEventListener('click', function () {
    var form = document.getElementById('pn-personal-finance-manager-form-settings');
    if (form) form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  });

  exportBtn.addEventListener('click', function () {
    var fd = new FormData();
    fd.append('action', 'pn_personal_finance_manager_ajax');
    fd.append('pn_personal_finance_manager_ajax_type', 'pn_personal_finance_manager_settings_export');
    fd.append('pn_personal_finance_manager_ajax_nonce', pn_personal_finance_manager_settings_footer.nonce);
    fetch(pn_personal_finance_manager_settings_footer.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.error_key) { if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_settings_footer.i18n.exportError, 'red'); return; }
        var blob = new Blob([JSON.stringify(res.settings, null, 2)], { type: 'application/json' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'pn-personal-finance-manager-settings-' + new Date().toISOString().slice(0, 10) + '.json';
        document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
      })
      .catch(function () { if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_settings_footer.i18n.exportError, 'red'); });
  });

  importBtn.addEventListener('click', function () { fileInput.value = ''; fileInput.click(); });

  fileInput.addEventListener('change', function () {
    var file = fileInput.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) {
      var data;
      try { data = JSON.parse(e.target.result); } catch (err) { if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_settings_footer.i18n.invalidFile, 'red'); return; }
      if (!confirm(pn_personal_finance_manager_settings_footer.i18n.confirmImport)) return;
      var fd = new FormData();
      fd.append('action', 'pn_personal_finance_manager_ajax');
      fd.append('pn_personal_finance_manager_ajax_type', 'pn_personal_finance_manager_settings_import');
      fd.append('pn_personal_finance_manager_ajax_nonce', pn_personal_finance_manager_settings_footer.nonce);
      fd.append('settings', JSON.stringify(data));
      fetch(pn_personal_finance_manager_settings_footer.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.error_key) { if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(res.error_content || pn_personal_finance_manager_settings_footer.i18n.importError, 'red'); return; }
          if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_settings_footer.i18n.importSuccess, 'green');
          setTimeout(function () { location.reload(); }, 1500);
        })
        .catch(function () { if (typeof pn_personal_finance_manager_get_main_message === 'function') pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_settings_footer.i18n.importError, 'red'); });
    };
    reader.readAsText(file);
  });
})();
