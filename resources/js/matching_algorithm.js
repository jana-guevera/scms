// function for fetching reqquired data
const fetchMatchDataForPair = async (currentMemberId, friendId) => {
    var currentMember = await fetch(getURL("members/read_one_with_survey.php?id=" + currentMemberId));
    currentMember = await currentMember.json();
    currentMember = currentMember.record;

    var friend = await fetch(getURL("members/read_one_with_survey.php?id=" + friendId));
    friend = await friend.json();
    friend = friend.record;

    var survey = await fetch(getURL("survey/read_all.php"));
    survey = await survey.json();
    survey = survey.records;

    var surveyCategories = await fetch(getURL("survey_categories/read_all.php"));
    surveyCategories = await surveyCategories.json();
    surveyCategories = surveyCategories.records;

    return {
        "currentMember": currentMember,
        "friend": friend,
        "survey": survey,
        "surveyCategories": surveyCategories
    };
}

// organize fetched data
const organizeFetchedData = (memberData, fetchedData) => {
    memberData.surveyCategories = [];

    fetchedData.surveyCategories.forEach(sc => {
        memberData.surveyCategories.push({
            ...sc
        });
    });

    memberData.memberSurvey.forEach(ms => {
        ms.survey = fetchedData.survey.find(s => s.id == ms.surveyId);
    });

    memberData.surveyCategories.forEach(sc => {
        sc.memberSurvey = [];
        sc.memberSurvey = memberData.memberSurvey.filter(ms => ms.survey.categoryId == sc.id);
    });

    return memberData;
}

// prepare base data to calculate match percentage
const prepareBaseData = (member) => {
    member.surveyCategories.forEach(sc => {
        sc.posibileAnswers = 0;

        sc.memberSurvey.forEach(ms => {
            if(ms.survey.inputType == 0){
                sc.posibileAnswers += 1;
            }else if(ms.survey.inputType == 1){
                sc.posibileAnswers += ms.survey.answers.split(",").length;
            }else if(ms.survey.inputType == 2){
                sc.posibileAnswers += 1;
            }
        });
    });

    return member;
}

// calculate matching percentage based on specific categories
const findMatchingPercentageOnCategory = (memberData, friendData) => {
    friendData.surveyCategories.forEach(friendCategory => {
        var memberCategory = memberData.surveyCategories.find(dt => dt.id == friendCategory.id);

        friendCategory.matchingAnswers = [];
        friendCategory.percentage = 0;

        friendCategory.memberSurvey.forEach(ms => {
            var memberSurvey = memberCategory.memberSurvey.find(e => e.surveyId == ms.surveyId);
            var memberAnswers = memberSurvey.answers.split(",");
            var friendAnswer = ms.answers.split(",");
    
            friendAnswer.forEach(ans => {
                if(memberAnswers.includes(ans)){
                    friendCategory.matchingAnswers.push(ans);
                }
            });
        });

        friendCategory.percentage = parseInt((friendCategory.matchingAnswers.length / memberCategory.posibileAnswers) * 100);
    });

    return friendData;
}

// calculate overall matching percentage
const calculateOverallPercentage = (memberData) => {
    memberData.overallPercentage = 0;

    memberData.surveyCategories.forEach(sc => {
        memberData.overallPercentage += sc.percentage;
    });

    memberData.overallPercentage = parseInt(memberData.overallPercentage / memberData.surveyCategories.length);

    return memberData;
}

// calculate matches 
const calculateMatch = async (currentMemberId, friendId) => {
    var fetchedData = await fetchMatchDataForPair(currentMemberId, friendId);
    var currentMember = organizeFetchedData(fetchedData.currentMember, fetchedData);
    var friend = organizeFetchedData(fetchedData.friend, fetchedData);

    currentMember = prepareBaseData(currentMember);
    friend = findMatchingPercentageOnCategory(currentMember, friend);
    friend = calculateOverallPercentage(friend);

    return friend;
}