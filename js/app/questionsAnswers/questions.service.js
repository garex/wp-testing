(function(angular, Wpt) {
    'use strict';

    angular.module('wptApp').factory('questionsService', questionsService);

    questionsService.$inject = ['QuestionCollection', 'scalesService'];
    function questionsService   (QuestionCollection,   scalesService) {
        var questions = QuestionCollection.fromArray(Wpt.questions);

        Wpt.globalAnswers.forEach(function(globalAnswer) {
            questions.addGlobalAnswer(globalAnswer.id, globalAnswer.title);
        });

        return questions.injectScales(scalesService);
    };
})(angular, Wpt);
