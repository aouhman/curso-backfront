import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';

@Component({
    selector: 'login',
    templateUrl: '../views/login.html',
    providers: [UserService]
})
export class LoginComponent implements OnInit{
     public  title: string;
     public  user;
     public  token;
     public  identity;
     constructor(
         private _route: ActivatedRoute,
         private _router: Router,
         private _userService: UserService
     ){
         this.title = 'Login';
         this.user  = {
             "email"    : "",
             "password" : "",
             "getHash"  : "true"
         }
     }
    ngOnInit(){
            this.logout();
            this.redirectIfIdentity() ;
    }
    logout(){
        this._route.params.forEach((params: Params)=>{
            let logout = +params['id'];
            if(logout == 1){
                localStorage.removeItem('identity');
                localStorage.removeItem('token');
                this.identity= null;
                this.token= null;
                window.location.href = '/login';
            }
        });
    }

    redirectIfIdentity(){
        let identity = this._userService.getIdentity();
        if(identity != null && identity.sub){
            window.location.href = "/";
        }
    }

    onSubmit(){
        this._userService.signup(this.user).subscribe(
            response => {
                this.identity = response;
                if(this.identity.length <=1) {
                    console.log("Error")
                }{
                    if(!this.identity.status){
                        localStorage.setItem('identity',JSON.stringify(this.identity));
                           this.user.getHash = null;
                           this._userService.signup(this.user).subscribe(
                            response => {
                            this.token = response;
                            if(this.identity.length <=1) {
                                console.log("Error")
                            }{
                                if(!this.identity.status){

                                    localStorage.setItem('token',JSON.stringify(this.token));
                                    this.redirectIfIdentity()
                                }
                            }
                        },
                                error => {
                            console.log(<any>error);
                        })

                    }
                }
            },
            error => {
                console.log(<any>error);
            }
        )
    }

}