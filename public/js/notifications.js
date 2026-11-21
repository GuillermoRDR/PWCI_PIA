(function () {
  'use strict';

  window.KdemyNotifications = {
    toast: function (options) {
      if (!window.Swal) return;

      return window.Swal.fire(Object.assign({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3600,
        timerProgressBar: true
      }, options || {}));
    }
  };
})();
