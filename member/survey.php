<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_SESSION['userId'];?>";

    document.getElementById("nav-survey").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Survey";
    document.getElementById("add-item-button").style.display = "none";

    const wrapper = document.getElementById("table-wrapper");

    wrapper.innerHTML = `<div id="category-wrapper"></div>
                        <div id="survey-wrapper"></div>`;

    const fetchData = async () => {
        var member = await fetch(getURL('members/read_one.php?id=' + memberId));
        member = await member.json();

        var memberSurvey = await fetch(getURL('member_survey/read_all_member_survey.php?id=' + memberId));
        memberSurvey = await memberSurvey.json();

        var surveyCategories = await fetch(getURL('survey_categories/read_all.php'));
        surveyCategories = await surveyCategories.json();

        var currentCategory = surveyCategories.records[0];
        var currentMemberSurvey = memberSurvey.records.filter(ms => ms.survey.categoryId == currentCategory.id);

        const callback = async () => {
            showLoader("btn-save-changes", "Submitting...");

            var memberAsnwers = [];

            currentMemberSurvey.forEach(ms => {
                if(ms.survey.inputType == 0){
                    var radioButtons = document.getElementsByName("question-" + ms.surveyId);

                    for(var i = 0; i < radioButtons.length; i++){
                        if(radioButtons[i].checked && ms.survey.answers.includes(radioButtons[i].value)){
                            memberAsnwers.push({
                                "memberId": memberId,
                                "surveyId": ms.surveyId,
                                "answers": radioButtons[i].value.trim()
                            });

                            break;
                        }
                    }
                }

                if(ms.survey.inputType == 1){
                    const selected = document.querySelectorAll(`#question-${ms.surveyId} option:checked`);
                    var selectedAnswers = [];

                    for(var i = 0; i < selected.length; i++){
                        if(ms.survey.answers.includes(selected[i].value)){
                            selectedAnswers.push(selected[i].value.trim());
                        }
                    }

                    if(selectedAnswers.length > 0){
                        memberAsnwers.push({
                            "memberId": memberId,
                            "surveyId": ms.surveyId,
                            "answers": selectedAnswers.toString()
                        });
                    }
                }

                if(ms.survey.inputType == 2){
                    var value = document.getElementById("question-" + ms.surveyId).value.trim();

                    if(value != "" && ms.survey.answers.includes(value)){
                        memberAsnwers.push({
                            "memberId": memberId,
                            "surveyId": ms.surveyId,
                            "answers": value
                        });
                    }
                }
            });

            if(memberAsnwers.length > 0){
                try{
                    var result = await fetch(getURL("member_survey/update_answers.php"), { 
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(memberAsnwers),
                    });

                    result = await result.json();

                    if(result.succ){
                        toastr.success(result.msg, "Sucess!");
                    }else{
                        toastr.error(result.msg, "Error!");
                    }
                }catch(error){
                    console.error(error);
                    toastr.error("Something went wrong. Please try again!", "Error!");
                }finally{
                    hideLoader("btn-save-changes", "Save Changes");
                }
            }
        }

        buildSurveyCategorySection(surveyCategories.records);
        buildQuestionFormSection(currentCategory, currentMemberSurvey, callback);

        const surveyCategoryInput = document.getElementById("surveyCategory");
        surveyCategoryInput.addEventListener('change', () => {
            currentCategory = surveyCategories.records.find(cat => cat.id == surveyCategoryInput.value);
            currentMemberSurvey = memberSurvey.records.filter(ms => ms.survey.categoryId == currentCategory.id);

            buildQuestionFormSection(currentCategory, currentMemberSurvey, callback);
        });
    }

    const buildSurveyCategorySection = (surveyCategories) => {
        const categoryWrapper = document.getElementById("category-wrapper");

        var selectionList = "";

        surveyCategories.forEach((cat, index) => {
            if(index == 0){
                selectionList += `<option selected value="${cat.id}">${cat.name}</option>`;
            }else{
                selectionList += `<option value="${cat.id}">${cat.name}</option>`;
            }
        });

        categoryWrapper.innerHTML = `
                            <div class="mb-3 row">
                                <label for="surveyCategory" class="col-form-label mx-3">Category</label>
                                <div class="col-sm-3 p-0">
                                    <select id="surveyCategory" class="form-control">
                                        ${selectionList}
                                    </select>
                                </div>
                            </div><hr>`;
    }

    const buildQuestionFormSection = (surveyCategory, memberSurvey, callback) => {
        const surveyWrapper = document.getElementById("survey-wrapper");

        var questions = "";
        var count = 1;

        memberSurvey.forEach(ms => {
            questions += generateForm(ms, count);
            count++;
        });

        surveyWrapper.innerHTML = `
        <div class="modal-content">

            <div class="modal-header">
                <h5>Question Related to ${surveyCategory.name}</h5>
            </div>

            <div class="modal-body">
                <form onsubmit="return false;">
                    <div class="row">
                        ${questions}
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btn-save-changes" class="btn btn-primary">Save changes</button>
            </div>

        </div>
        `;

        const btnSaveChanges = document.getElementById("btn-save-changes");
        btnSaveChanges.addEventListener("click", callback);
    }

    const generateForm = (memberSurvey, index) => {
        if(memberSurvey.survey.inputType == 0){
            var binaryAnswers = memberSurvey.survey.answers.split(",");
            var selectedRadio = "";

            if(memberSurvey.answers != "" && binaryAnswers.indexOf(memberSurvey.answers) == 0){
                return `
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>${index}: ${memberSurvey.survey.question}</strong></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="${binaryAnswers[0]}" name="question-${memberSurvey.surveyId}" id="radioBtn1-${memberSurvey.surveyId}" checked>
                                <label class="form-check-label" for="radioBtn1-${memberSurvey.surveyId}">
                                    ${binaryAnswers[0]}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="${binaryAnswers[1]}" name="question-${memberSurvey.surveyId}" id="radioBtn2-${memberSurvey.surveyId}">
                                <label class="form-check-label" for="radioBtn2-${memberSurvey.surveyId}">
                                    ${binaryAnswers[1]}
                                </label>
                            </div>
                        </div>
                    </div>
                `;
            }else{
                return `
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>${index}: ${memberSurvey.survey.question}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="${binaryAnswers[0]}" name="question-${memberSurvey.surveyId}" id="radioBtn1-${memberSurvey.surveyId}">
                                <label class="form-check-label" for="radioBtn1-${memberSurvey.surveyId}">
                                    ${binaryAnswers[0]}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="${binaryAnswers[1]}" name="question-${memberSurvey.surveyId}" id="radioBtn2-${memberSurvey.surveyId}" checked>
                                <label class="form-check-label" for="radioBtn2-${memberSurvey.surveyId}">
                                    ${binaryAnswers[1]}
                                </label>
                            </div>
                        </div>
                    </div>
                `;
            }

            
        }

        if(memberSurvey.survey.inputType == 1){
            var memberAnswers = memberSurvey.answers.split(",");
            var selectionList = "";
            var answers = memberSurvey.survey.answers.split(",");

            answers.forEach(a => {
                if(memberAnswers.indexOf(a.trim()) >= 0){
                    selectionList += `<option selected value="${a.trim()}">${a.trim()}</option>`;
                }else{
                    selectionList += `<option value="${a.trim()}">${a.trim()}</option>`;
                }
            });

            // if(memberSurvey.answers == "" || memberSurvey.answers == undefined){
            //     selectionList += "<option selected disabled>Select your preferred answers</option>";
            // }

            return `
                <div class="col-md-6">
                    <div class="form-group">
                        <label>${index}: ${memberSurvey.survey.question}</label>
                        <select class="form-control" id="question-${memberSurvey.surveyId}" size="3" multiple aria-label="multiple select example">
                            ${selectionList}
                        </select>
                    </div>
                </div>
            `;
        }

        if(memberSurvey.survey.inputType == 2){
            var answers = memberSurvey.survey.answers.split(",");
            var selectionList = "";

            answers.forEach(a => {
                if(memberSurvey.answers == a.trim()){
                    selectionList += `<option selected value="${a.trim()}">${a.trim()}</option>`; 
                }else{
                    selectionList += `<option value="${a.trim()}">${a.trim()}</option>`; 
                }
            });

            // if(memberSurvey.answers == "" || memberSurvey.answers == undefined){
            //     selectionList += "<option selected disabled>Select your preferred answer</option>";
            // }

            return `
                <div class="col-md-6">
                    <div class="form-group">
                        <label>${index}: ${memberSurvey.survey.question}</label>
                        <select id="question-${memberSurvey.surveyId}" class="form-control">
                            <option selected disabled>Select your preferred answer</option>
                            ${selectionList}
                        </select>
                    </div>
                </div>
            `;
        }

        return "";
    }

    fetchData();
</script>