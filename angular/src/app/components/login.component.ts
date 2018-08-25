
import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';

@Component({
    selector: 'login',
    templateUrl: '../views/login.html',
    providers: [UserService]
})
export class LoginComponent implements OnInit{
     public title: string;
     public  user;
     constructor(
         private _route: ActivatedRoute,
         private _router: Router,
         private _userService: UserService
     ){
         this.title = 'Login';
         this.user  = {
             "email"    : "",
             "password" : "",
             "gethash"  : "true"
         }
     }
    ngOnInit(){
        console.log(JSON.parse(localStorage.getItem('identity')));
    }

    onSubmit(){
        this._userService.signup(this.user).subscribe(
            response => {
                this.identity = response;
                if(this.identity.lenght <=1) {
                    console.log("Error")
                }{
                    if(!this.identity.status){
                        localStorage.setItem('identity',JSON.stringify(this.identity));
                           this.user.getHash = 'false';
                           this._userService.signup(this.user).subscribe(
                            response => {
                            this.token = response;
                            if(this.identity.lenght <=1) {
                                console.log("Error")
                            }{
                                if(!this.identity.status){
                                    localStorage.setItem('token',JSON.stringify(this.token));
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