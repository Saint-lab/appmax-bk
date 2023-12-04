AppMax API Endpoints and Parameters
##############################

Register User | POST Request to http://localhost/appmax/api/register
	Parameter{name, email, password, confirmed}

User Login | POST Request to http://localhost/appmax/api/login
	Parameter{email, password}

Forgot Password | POST Request to http://localhost/appmax/api/reset	
	Parameter{email}

get Password reset token | GET Request to http://localhost/appmax/api/password/reset/{token}

Reset Password | POST Request to http://localhost/appmax/api/password/reset

User Profile | GET Request to http://localhost/appmax/api/profile
Update Profile | POST Request to http://localhost/appmax/api/profile
	Parameter{name, email}

change-password | POST Request to http://localhost/appmax/api/change-password
	Parameter{current_password, password, confirmed}

profile-image | GET Request to http://localhost/appmax/api/profile-image
change profile image | POST Request to http://localhost/appmax/api/profile-image	
	Parameter{user_image}

