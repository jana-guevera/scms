<?php include "./layout/layout.php"; ?>

<!-- page layout and table populating -->
<script>
    const memberId = "<?php echo $_GET['id'];?>";

    document.getElementById("nav-members").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Find Friends";
    document.getElementById("add-item-button").style.display = "none";

    const breadcumbs = document.getElementById("breadcumbs");
    breadcumbs.style.display = "flex";
    breadcumbs.innerHTML = `<li><a href="#" onClick="window.history.back();">Members</a></li>
                            <li><a href="member_friends_matcher.php?id=${memberId}">Find Friends</a></li>`;

    const wrapper = document.getElementById("table-wrapper");

    wrapper.innerHTML = `<div id="search-wrapper" class="row">
                            <div class="col-4">
                                    <form onsubmit="return false;">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search Keyword" id="search-input">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit" id="search-button">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="matches-list-wrapper">
                            <div class="row" id="matches-card-container"></div>
                        </div>`;

    var searchInput = document.getElementById("search-input");

    document.getElementById("search-button").addEventListener("click", () => {
        fetchData(searchInput.value);
    });
</script>

<!-- fetch data and calculate matchings -->
<script>
    const fetchData = async (searchQuery) => {
        var url = searchQuery == "" ? getURL("members/get_offline_members_with_survey.php") 
                                    : getURL("members/search_offline_members_with_survey.php?id=" + memberId + "&search=" + searchQuery);

        var members = await fetch(url);
        members = await members.json();
        members = members.records;

        if(members.length <= 1){
            document.getElementById("matches-card-container").innerHTML = `<h3 class="ml-3">No Matches Found!</h3>`;
            return;
        }

        var memberFriends = await fetch(getURL("matched_friends/read_member_friends.php?id=" + memberId));
        memberFriends = await memberFriends.json();
        memberFriends = memberFriends.records;

        members = members.filter(m => {
            if(m.id == memberId) { return true; }

            for(var i = 0; i < memberFriends.length; i++){
                var mf = memberFriends[i];

                if(mf.memberId == m.id || mf.friendId == m.id){
                    if(mf.status == 2){
                        return true;
                    }

                    return false;
                }
            }

            return true;
        });

        var survey = await fetch(getURL("survey/read_all.php"));
        survey = await survey.json();
        survey = survey.records;

        var surveyCategories = await fetch(getURL("survey_categories/read_all.php"));
        surveyCategories = await surveyCategories.json();
        surveyCategories = surveyCategories.records;

        var currentMember;
        var filteredMembers = [];

        members.forEach(m => {
            m.surveyCategories = [];

            surveyCategories.forEach(sc => {
                m.surveyCategories.push({
                    ...sc
                });
            });

            m.memberSurvey.forEach(ms => {
                ms.survey = survey.find(s => s.id == ms.surveyId);
            });

            m.surveyCategories.forEach(sc => {
                sc.memberSurvey = [];
                sc.memberSurvey = m.memberSurvey.filter(ms => ms.survey.categoryId == sc.id);
            });

            if(m.id == memberId){
                currentMember = m;
            }else{
                filteredMembers.push(m);
            }
        });

        var preparedData = prepareData(currentMember);

        findMatchings(preparedData, filteredMembers);

        filteredMembers.sort((a, b) => {
            if(a.overallPercentage < b.overallPercentage) return 1;
            if(a.overallPercentage > b.overallPercentage) return -1;
            return 0;
        });

        displayMatches(currentMember, filteredMembers);

        var viewButtons = document.querySelectorAll(".btn-view-match");
        var friendRequestBtn = document.querySelectorAll(".send-request-btn");

        for(var i = 0; i < viewButtons.length; i++){
            viewButtons[i].addEventListener("click", (event) => {
                viewParticularMatch(currentMember, filteredMembers.find(e => e.id == event.target.id));
            });
        }

        for(var i = 0; i < friendRequestBtn.length; i++){
            friendRequestBtn[i].addEventListener("click", (event) => {
                var id = event.target.id.split("-")[2];
                sendFriendRequest(currentMember, filteredMembers.find(e => e.id == id));
            });
        }
    }

    const prepareData = (currentMemberSurvey) => {
        currentMemberSurvey.surveyCategories.forEach(sc => {
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

        return currentMemberSurvey;
    }
    
    const findMatchings = (preparedData, observingMembers) => {

        observingMembers.forEach(om => {
            om.surveyCategories.forEach(sc => {
                var observer = preparedData.surveyCategories.find(d => d.id == sc.id);
                findMatchingsOnCategory(observer, sc);
            });

            calculateOverallPercentage(om);
        });
    }

    const findMatchingsOnCategory = (observerData, memberData) => {
        memberData.matchingAnswers = [];
        memberData.percentage = 0;

        memberData.memberSurvey.forEach(ms => {
            var observeyMemberSurvey = observerData.memberSurvey.find(e => e.surveyId == ms.surveyId);
            var observerAnswers = observeyMemberSurvey.answers.split(",");
            var observingMemberAnswer = ms.answers.split(",");

            observingMemberAnswer.forEach(ans => {
                if(observerAnswers.includes(ans)){
                    memberData.matchingAnswers.push(ans);
                }
            });
        });

        memberData.percentage = parseInt((memberData.matchingAnswers.length / observerData.posibileAnswers) * 100);
    }

    const calculateOverallPercentage = (observingMember) => {
        observingMember.overallPercentage = 0;

        observingMember.surveyCategories.forEach(sc => {
            observingMember.overallPercentage += sc.percentage;
        });

        observingMember.overallPercentage = parseInt(observingMember.overallPercentage / observingMember.surveyCategories.length);
    }

    fetchData("");
 
</script>

<!-- display matches -->
<script>
    const displayMatches = (currentMember, observingMembers) => {
        var cardContainer = document.getElementById("matches-card-container");
        cardContainer.innerHTML = "";

        observingMembers.forEach(om => {
        
            cardContainer.innerHTML += `
            <div class="col-4 pb-3 ">

                <div class="card" style="width: 20rem;">
                    <div class="card-image-container">
                        <div class="overall-percentage">${om.overallPercentage}%</div>
                        <img class="card-img-top" src="../../resources/images/uploads/profile/${om.image}" alt="Card image cap">
                    </div>

                    <div class="card-body">
                    <h5 class="card-title mb-0">${om.name}</h5>
                    <p>${om.gender}</p>

                    <button class="btn btn-primary btn-sm send-request-btn" id="send-request-${om.id}">Send Request</button>
                    <button class="btn btn-secondary btn-sm btn-view-match" id="${om.id}">View</button>
                    </div>
                </div>
            </div>`;
        });
    }
</script>

<!-- view particular match -->
<script>
    const viewParticularMatch = (currentMember, observingMember) => {
        var categoryPercentage = "";

        observingMember.surveyCategories.forEach(sc => {
            categoryPercentage += `#${sc.name}: ${sc.percentage}% </br>`;
        });


        
        document.querySelector('.modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="addRecordBtn-${observingMember.id}" class="btn btn-primary">Send Request</button>`;

        document.querySelector('#modalLongTitle').innerHTML = "Match Details";

        var htmlContent = 
        `<div>
            <div class="row">
                <div class="col-6 view-modal-image">
                    <img class="w-100" src="../../resources/images/uploads/profile/${observingMember.image}" alt="">
                </div>
                <div class="col-6">
                    <label class="view-control">Matching Percentage: <strong class="short-text">${observingMember.overallPercentage}%</strong></label>
                    <label class="view-control">Name: <strong class="short-text">${observingMember.name}</strong></label>
                    <label class="view-control">Gender: <strong class="short-text">${observingMember.gender}</strong></label>
                    <label class="view-control">Specific Matchings: 
                        <strong class="long-text">
                            ${categoryPercentage}
                        </strong>
                    </label>
                </div>                     
            </div>
        </div>`;

        document.querySelector('.modal-body').innerHTML = htmlContent;
        
        $('#ModalCenter').modal('toggle');

        document.getElementById(`addRecordBtn-${observingMember.id}`).addEventListener("click", () => {
            sendFriendRequest(currentMember, observingMember);
            $('#ModalCenter').modal('toggle');
        });
    }
</script>

<!-- send friend request -->
<script>
  const sendFriendRequest = async (currentMember, observingMember) => {
    try{
        var result = await fetch(getURL("matched_friends/create.php"), { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                "memberId": memberId,
                "friendId": observingMember.id
            }),
        });

        result = await result.json();
        if(result.succ){
            toastr.success(result.msg, "Sucess!");
            fetchData("");
        }else{
            toastr.error(result.msg, "Error!");
        }
    }catch(error){
        console.error(error);
        toastr.error("Something went wrong. Please try again!", "Error!");
    }
  }
</script>