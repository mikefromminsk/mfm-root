angular.module("App", []).controller("Controller", function ($scope, $http) {
    $scope.tracks = [
        {length: "1:29", title: "Ваше благородие, госпожа Удача"},
        {length: "3:01", title: "Мы за ценой не постоим"},
        {length: "1:58", title: "Грузинская песня"},
        {length: "4:59", title: "Бери шинель, пошли домой"},
        {length: "2:07", title: "Надежды маленький оркестрик"},
        {length: "2:35", title: "Старинная солдатская песня"},
        {length: "1:07", title: "Бумажный солдат"},
        {length: "2:50", title: "До свидания, мальчики"},
        {length: "2:55", title: "Исторический роман"},
        {length: "2:59", title: "Белорусский вокзал"},
        {length: "3:20", title: "Молитва"},
        {length: "1:38", title: "Ах, Надя, Наденька"},
        {length: "3:06", title: "Когда воротимся мы в Портленд"},
        {length: "2:32", title: "Песенка об Арбате"},
        {length: "1:43", title: "Дежурный по апрелю"},
        {length: "1:12", title: "Песенка про чёрного кота"},
        {length: "3:08", title: "Капли датского короля"},
        {length: "1:39", title: "Пожелание друзьям"},
        {length: "1:29", title: "Песенка Верещагина (Ваше благородие…) из фильма «Белое солнце пустыни»"},
        {length: "1:07", title: "Голубой шарик"},
    ]

    $scope.artist = {
        name: "Булат Окуджава",
        logo: "img/logo.jpg",
    }

    $scope.likes = {
        count: 12312123,
        fiends: [
            "Noize MC",
            "Баста",
            "L`One",
        ],
    }
})