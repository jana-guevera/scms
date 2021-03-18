<script>
    const userId = "<?php echo $user['id'] ?>";
    const userName = "<?php echo $user['name'] ?>";
    const mobileNo = "<?php echo $user['mobileNo'] ?>";

    var profileForm = `
    <div class="card form-wrapper">
        <div class="card-header">Profile Setting</div>
        <div class="card-body">
            <form onsubmit="update(); return false;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Name <span style="color:red;">*</span></label>
                            <input type="text" id="name" class="form-control" value="${userName}" required="required">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="mobileNo">Mobile No <span style="color:red;">*</span></label>
                            <input type="number" id="mobileNo" class="form-control" value="${mobileNo}" required="required">
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

    const update = async () => {
        showLoader("update-btn", "Updating...");
        const imageFile = document.querySelector("#profileImage");

        var data = {
            'id': userId,
            'name': document.querySelector("#name").value.trim(),
            'mobileNo': document.querySelector("#mobileNo").value.trim(),
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

            var result = await fetch(getURL("staffs/update_user_profile.php"), {
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