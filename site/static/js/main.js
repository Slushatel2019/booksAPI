$(function () {
    $('#templModalsSign').loadTemplate("static/templates/modalsSign.html");
    document.cookie = "token=555; max-age=1";
    $.ajax({
        url: "api/books/",
        type: "GET",
        xhrFields: {
            withCredentials: true
        },
        crossDomain: true,
        success: function (data) {
            $('#result').empty();
            $('#result').append($('<table class="table table-striped"> <thead>' +
                '<tr> <th>#</th> <th>name</th> <th>genre</th> <th>pages</th>' +
                '</tr> </thead> <tbody id="tbody"> </tbody></table>'));
            let booksArrayOfObjects = JSON.parse(data).data;
            let i = 0;
            $.each(booksArrayOfObjects, function (key, bookObject) {
                let tds = '';
                if (i < 7) {
                    $.each(bookObject, function (index, value) {
                        tds += '<td>' + value + '</td>';
                    });
                    $('#tbody').append($('<tr>' + tds + '</tr>'));
                    i++;
                }
                else return false;
            });
            $('table .btn').on('click', function () {
                $("#alertGuest").modal('show');
            });
        }
    });
    $('#nav').on('click', '#showAllBooks', function () {
        $.ajax({
            url: "api/books/",
            type: "GET",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    $('#result').empty();
                    $('#result').append($('<table class="table table-striped"> <thead>' +
                        '<tr> <th>#</th> <th>name</th> <th>genre</th> <th>pages</th>' +
                        '<th>Actions</th> </tr> </thead> <tbody id="tbody"> </tbody></table>'));
                    let booksArrayOfObjects = JSON.parse(data).data;
                    $.each(booksArrayOfObjects, function (key, bookObject) {
                        let tds = '';
                        $.each(bookObject, function (index, value) {
                            tds += '<td>' + value + '</td>';
                        });
                        $('#tbody').append($('<tr>' + tds +
                            '<td style="width:10%"><div class="btn-group">' +
                            '<button class="btn btn-success" style="display:none" type="button">Update</button>' +
                            '<button class="btn btn-warning" type="button">Change</button>' +
                            '<button class="btn btn-danger" type="button">Delete</button>' +
                            '</div></td></tr>'));
                    });
                    $('table .btn.btn-warning').on('click', function () {
                        $(this).css("display", "none");
                        $(this).prev().css("display", "");
                        $('table .btn.btn-danger').prop({ disabled: true });
                        $('table .btn.btn-warning').prop({ disabled: true });
                        let i = 2;
                        while (i < 5) {
                            let td = $(this).closest("tr").find("td:nth-child(" + i + ")");
                            let oldName = td.text();
                            td.empty();
                            td.append('<input type="text">');
                            $(td[0]['firstChild']).val(oldName);
                            i++;
                        }
                    });
                    $('table .btn.btn-success').on('click', function () {
                        $(this).css("display", "none");
                        $(this).next().css("display", "");
                        $('table .btn.btn-danger').prop({ disabled: false });
                        $('table .btn.btn-warning').prop({ disabled: false });
                        let i = 2;
                        let bookElements = [];
                        let tr = $(this).closest("tr");
                        let id = tr.find("td:first-child").text();
                        while (i < 5) {
                            let td = tr.find("td:nth-child(" + i + ")");
                            let newName = $(td[0]['firstChild']).val();
                            bookElements[i] = newName;
                            td.empty();
                            td.append(newName);
                            i++;
                        }
                        let newBook = {
                            name: bookElements[2],
                            genre: bookElements[3],
                            pages: bookElements[4],
                        };
                        if (book = checkInputBook(newBook)) {
                            $.ajax({
                                url: "../api/books/" + id,
                                type: "PUT",
                                data: JSON.stringify(book),
                                success: function (data) {
                                    if (JSON.parse(data).status == 403) {
                                        notSignIn();
                                    } else {
                                        let answer = JSON.parse(data);
                                        if (answer.status == 200 && answer.message['changed book'] == 1) {
                                            alert('book is changed');
                                        }
                                        else {
                                            alert('book is not changed');
                                        }
                                    }
                                }
                            });
                        } else {
                            alert('empty fields or incorrect data');
                            $(this).next().click();
                        }
                    });
                    $('.btn.btn-danger').on('click', function () {
                        del($(this), 'books');
                    });
                }
            }
        });
    });
    $('#templModals').on('click', '#showOneBook', function () {
        let bookNumber = $('#bookNumber').val();
        if (bookNumber != false) {
            $('#modalShowOneBook').modal('hide');
            $.ajax({
                url: "api/books/" + bookNumber,
                type: "GET",
                success: function (data) {
                    if (JSON.parse(data).status == 403) {
                        notSignIn();
                    } else {
                        let book = JSON.parse(data);
                        if (book.data == false) {
                            alert('no book with id = ' + bookNumber);
                        }
                        else {
                            $('#result').empty();
                            $('#result').append($('<table class="table table-striped" id="table">' +
                                '<thead> <tr> <th>#</th> <th>name</th> <th>genre</th>' +
                                '<th>pages</th> </tr> </thead> </table>'));
                            $('#table').append($('<tbody> <tr> <th>' + book.data.id + '</th>' +
                                '<th>' + book.data.name + '</th> <th>' + book.data.genre + '</th>' +
                                '<th>' + book.data.pages + '</th> </tr> </tbody> '));
                        }
                    }
                }
            });
        }
        else {
            alert('enter a book number');
        }
    });
    $('nav').on('click', '#countAllBooks', function () {
        $.ajax({
            url: "api/books/count",
            type: "GET",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    amount = JSON.parse(data).data;
                    $('#showCount').text(amount);
                }
            }
        });
    });
    $('#templModals').on('click', '#addBook', function () {
        let book = {
            name: $('#name').val(),
            genre: $('#genre').val(),
            pages: $('#pages').val(),
        }
        if (checkInputBook(book)) {
            $('#modalAddBook').modal('hide');
            $.ajax({
                url: "api/books/",
                type: "POST",
                data: JSON.stringify(book),
                success: function (data) {
                    if (JSON.parse(data).status == 403) {
                        notSignIn();
                    } else {
                        alert(JSON.parse(data).message);
                    }
                }
            });
        }
        else {
            alert('empty fields or incorrect data');
        }
    });
    $('#templModalsSign').on('click', '#signInModalButton', function () {
        let user = {
            login: $('#signInLogin').val(),
            password: $('#signInPassword').val(),
        }
        $('#modalSignIn').modal('hide');
        $.ajax({
            url: "api",
            type: "POST",
            data: JSON.stringify(user),
            success: function (data) {
                let response = JSON.parse(data).message;
                let userType = JSON.parse(data).data;
                if (response == 'ok') {
                    $('#userLogin').text('hello, ' + user.login + ' (' + userType + ')').css('color', 'white');
                    $('#navButtonSignIn').css("display", "none");
                    $('#navButtonSignUp').css("display", "none");
                    $('#navButtonLogOut').css("display", "");
                    $('#result').empty();
                    let obj = [
                        { name: 'show all books', setId: 'showAllBooks', setClass: 'btn btn-success m-1', setType: 'button' },
                        { name: 'show a specific book', setClass: 'btn btn-primary m-1', setType: 'button', setDataToggle: 'modal', setDataTarget: '#modalShowOneBook' },
                        { name: 'count all books', setId: 'countAllBooks', setClass: 'btn btn-success m-1', setType: 'button', setDataToggle: 'modal', setDataTarget: '#modalCountBooks' },
                        { name: 'add a book', setClass: 'btn btn-primary m-1', setType: 'button', setDataToggle: 'modal', setDataTarget: '#modalAddBook' }
                    ];
                    $('#nav').loadTemplate("static/templates/buttons.html", obj);
                    $('#templModals').loadTemplate("static/templates/modals.html");
                    if (userType == 'admin') {
                        $('#navButtonUsers').css("display", "");
                        $('#templModalAddUser').loadTemplate("static/templates/addUserForm.html");
                    }
                } else {
                    alert(response);
                }
            }
        });
    });
    $('#templModalsSign').on('click', '#signUpModalButton', function () {
        if ($('#signUpPassword').val() != $('#signUpConfirmPassword').val()) {
            alert('passwords are not the same');
        } else {
            let user = {
                login: $('#signUpLogin').val(),
                email: $('#signUpEmail').val(),
                password: $('#signUpPassword').val(),
                userType: 'user'
            }
            if (checkAddUserForm(user)) {
                $.ajax({
                    url: "api",
                    type: "POST",
                    data: JSON.stringify(user),
                    success: function (data) {
                        let response = JSON.parse(data).message
                        if (response == 'ok') {
                            alert('successful registration');
                            $('#modalSignUp').modal('hide');
                            $('#modalSignIn').modal('show');
                        } else {
                            alert(response);
                        }
                    }
                });
            } else {
                alert('incorrect input data or empty fields');
            }
        }
    });
    $('#navButtonLogOut').on('click', function () {
        document.cookie = "token=; max-age=0";
        location.reload();
    });
    $('#templModalAddUser').on('click', '#addNewUser', function () {
        if ($('#addNewPassword').val() != $('#addNewConfirmPassword').val()) {
            alert('passwords are not the same');
        } else {
            let user = {
                login: $('#addNewLogin').val(),
                email: $('#addNewEmail').val(),
                password: $('#addNewPassword').val(),
            }
            if (checkAddUserForm(user)) {
                function getUserType() {
                    let radio = $('[name="Radios"]');
                    for (let i = 0; i < radio.length; i++) {
                        if (radio[i].checked) {
                            return radio[i].value;
                        }
                    }
                }
                user.userType = getUserType();
                $.ajax({
                    url: "api/users/",
                    type: "POST",
                    data: JSON.stringify(user),
                    success: function (data) {
                        if (JSON.parse(data).status == 403) {
                            $('#modalAddUser').modal('hide');
                            notSignIn();
                        } else {
                            let response = JSON.parse(data).message
                            if (response == 'ok') {
                                alert('user has been added');
                                $('#modalAddUser').modal('hide');
                            } else {
                                alert(response);
                            }
                        }
                    }
                });
            } else {
                alert('incorrect input data or empty fields');
            }
        }
    });
    $('#ButtonShowUserList').on('click', function () {
        $.ajax({
            url: "api/users/",
            type: "GET",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    $('#result').empty();
                    $('#result').append($('<table class="table table-striped"> <thead>' +
                        '<tr> <th>id</th> <th>login</th> <th>email</th> <th>password</th>' +
                        '<th>userType</th> <th>token</th>' +
                        '<th>Actions</th> </tr> </thead> <tbody id="tbody"> </tbody></table>'));
                    let arrayOfUsers = JSON.parse(data).data;
                    $.each(arrayOfUsers, function (key, user) {
                        let tds = '';
                        $.each(user, function (index, value) {
                            tds += '<td>' + value + '</td>';
                        });
                        $('#tbody').append($('<tr>' + tds + '<td style="width:10%"><div>' +
                            '<button class="btn btn-danger" type="button">Delete</button>' +
                            '</div></td></tr>'));
                    });
                    $('.btn.btn-danger').on('click', function () {
                        del($(this), 'users');
                    });
                }
            }
        });
    });
});
function del(objButton, path) {
    if (confirm("Are you sure?")) {
        let tr = objButton.closest("tr");
        let id = tr.find("td:first-child").text();
        $.ajax({
            url: "api/" + path + "/" + id,
            type: "DELETE",
            success: function (data) {
                if (JSON.parse(data).status == 403) {
                    notSignIn();
                } else {
                    let answer = JSON.parse(data);
                    if (answer.status == 200) {
                        alert(answer.message);
                        tr.remove();
                    }
                    else {
                        alert(answer.message);
                    }
                }
            }
        });
    }
    else {
        return false;
    }
}
function checkInputBook(book) {
    for (let key in book) {
        let value = $.trim(book[key]);
        if (value.length == 0) {
            return false;
        }
        else {
            book[key] = value;
        }
    }
    let regExpWord = /^([A-Za-z]+[\s]?[A-Za-z]?)+$/;
    let checkName = regExpWord.test(book.name);
    let checkGenre = regExpWord.test(book.genre);
    let regExpNumber = /^[0-9]*$/;
    let checkPages = regExpNumber.test(book.pages);
    let result = (checkName && checkGenre && checkPages) ? book : false;
    return result;
};
function checkAddUserForm(user) {
    for (let key in user) {
        let value = $.trim(user[key]);
        if (value.length == 0) {
            return false;
        }
        else {
            user[key] = value;
        }
    }
    let regExpLogin = /[^A-Za-z0-9_]/;
    let checkLogin = regExpLogin.test(user.login);
    let regExpEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    let checkEmail = regExpEmail.test(user.email);
    let regExpPassword = /[^A-Za-z0-9_-]/;
    let checkPassword = regExpPassword.test(user.password);
    let result = (!checkLogin && checkEmail && !checkPassword) ? user : false;
    return result;
}
function notSignIn() {
    $('#alertGuest').modal('show');
    $('#alertGuest').on('hidden.bs.modal', function () {
        location.reload();
    })
};

