$(document).ready(function () {
  $("#register-form")
    .bootstrapValidator()
    .on('success.form.bv', function (e) {
      e.preventDefault();
    })
    .submit(function () {
      var parameter = {
        username: $('#email').val(),
        password: $('#psw').length ? $.sha1($('#psw').val()) : '',
        confirm: $('#conf').length ? $.sha1($('#conf').val()) : '',
        name: ''
      };
      jsLIB.ajax({
        url: `${jsLIB.rootDir}app/api/login/`, data: { MethodName: 'register', data: parameter },
        success: function (data, jqxhr) {
          if (data.register == true) {
            window.location.replace(data.page);
          } else {
            registerError(data.message);
          }
        }
      });
    });
});

function registerError(errorMessage) {
  jsLIB.dialogBox({
    title: 'Registro não efetivado!',
    message: errorMessage,
    type: BootstrapDialog.TYPE_WARNING,
    size: BootstrapDialog.SIZE_SMALL,
    draggable: true,
    closable: true,
    closeByBackdrop: false,
    closeByKeyboard: false,
    buttons: [{
      label: 'Fechar',
      cssClass: 'btn-info',
      action: function (dialogRef) {
        dialogRef.close();
      }
    }]
  });
}
