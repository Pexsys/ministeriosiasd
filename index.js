$(document).ready(function () {
  const loginError = msg => jsLIB.dialogBox({
    title: 'Erro',
    message: (errorMessage != '' ? errorMessage : 'Acesso negado!'),
    type: BootstrapDialog.TYPE_DANGER,
    size: BootstrapDialog.SIZE_SMALL,
    draggable: true,
    closable: true,
    closeByBackdrop: false,
    closeByKeyboard: false,
    buttons: [{
      label: 'Fechar',
      cssClass: 'btn-danger',
      action: function (dialogRef) {
        dialogRef.close();
      }
    }]
  });

  $("#login-form")
    .bootstrapValidator()
    .on('success.form.bv', function (e) {
      e.preventDefault();
    })
    .submit(function (e) {
      e.preventDefault();
      jsLIB.ajax({
        url: `${jsLIB.rootDir}app/api/login/`,
        waiting: false,
        async: true,
        data: { MethodName: 'login', username: $('#email').val(), password: $.sha1($('#psw').val()) },
        success: (data, jqxhr) => {
          e.preventDefault();
          console.log(data, jqxhr);
          // if (data.login == true) window.location.replace(data.page);
          // else loginError(data.message);
        },
        // error: loginError,
      });
    });
});
