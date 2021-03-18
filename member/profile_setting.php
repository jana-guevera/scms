<?php include "./layout/layout.php"; ?>

<script>
    const userId = "<?php echo $_SESSION['userId'] ?>";

    document.getElementById("nav-profile").classList.add("active-page");
    document.getElementById("pageTitle").textContent = "Profile Settings";
    document.getElementById("add-item-button").style.display = "none";

    const fetchData = async () => {
        var member = await fetch(getURL("members/read_one.php?id=" + userId));
        member = await member.json();
        member = member.record;

        var profileForm = `
        <div class="card form-wrapper">
            <div class="card-header">Profile Setting</div>
            <div class="card-body">
                <form onsubmit="update(); return false;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Name <span style="color:red;">*</span></label>
                                <input type="text" id="name" class="form-control" value="${member.name}" required="required">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobileNo">Mobile No <span style="color:red;">*</span></label>
                                <input type="number" id="mobileNo" class="form-control" value="${member.mobileNo}" required="required">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender <span style="color:red;">*</span></label>
                                <select id="gender" class="form-control" required="required">
                                    <option selected disabled value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address <span style="color:red;">*</span></label>
                                <input type="text" id="address" class="form-control" value="${member.address}" required="required">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="profileImage">Profile Image</label>
                                <input type="file" id="profileImage" class="form-control-file" placeholder="Please upload your profile photo">
                                <div class="mt-1"><a href="">Password Setting</a></div>
                            </div>
                        </div>

                        <div class="col-md-12 text-right">
                            <button type="submit" id="update-btn" class="btn btn-success">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>`;

        document.getElementById("main-bottom").innerHTML = profileForm;
        document.getElementById("gender").value = member.gender;
    };

    fetchData();

    const update = async () => {
        showLoader("update-btn", "Updating...");
        const imageFile = document.querySelector("#profileImage");

        var data = {
            'id': userId,
            'name': document.querySelector("#name").value.trim(),
            'mobileNo': document.querySelector("#mobileNo").value.trim(),
            'gender': document.querySelector("#gender").value.trim(),
            'address': document.querySelector("#address").value.trim(),
            'imageName': null
        }

        try {
            if (imageFile.value.length != 0) {
                const result = await uploadImage(imageFile);

                if (result.error) {
                    alertError(result.error);
                    return;
                }

                data.imageName = result.imageName;
            }

            var result = await fetch(getURL("members/update_user_profile.php"), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            result = await result.json();

            if(result.succ){
                toastr.success(result.msg, "Success!");
            }else{
                toastr.error(result.msg, "Error!");
            }
        } catch (error) {
            console.error(error);
        } finally {
            hideLoader("update-btn", "Update Profile");
        }

    }

    const uploadImage = async (fileInput) => {
        const formdata = new FormData();
        formdata.append('image', fileInput.files[0]);

        const response = await fetch(getURL("storage/upload_profile_image.php"), {
            method: 'POST',
            body: formdata
        });

        const result = await response.json();
        return result;
    }
</script>