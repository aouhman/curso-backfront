


import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../models/user';
import { UserService } from '../services/user.service';

@Component({
    selector: 'user-edit',
    templateUrl: '../views/user.edit.html',
    providers: [UserService]
})
export class UserEditComponent implements OnInit{
     public title: string;
     public status;
     public identity;
     public token;
     public user: User;

     constructor(
     private _route: ActivatedRoute,
     private _router: Router,
     private _userService: UserService

     ){
         this.title = 'Modification des données';
         this.identity = this._userService.getIdentity();
         this.token = this._userService.getToken();
     }
    ngOnInit(){
       if(this.identity == null){
           this._router.navigate(['/login']);
       }else{
           this.user = new User(
               this.identity.sub,
               this.identity.role,
               this.identity.name,
               this.identity.surname,
               this.identity.email,
               this.identity.password
           );
       }
    }
    onSubmit(){
        this._userService.update_user(this.user).subscribe(
                response =>{
                this.status = response.status;
                if(response.status != 'success'){
                    this.status =  'error';
                }else{
                    localStorage.setItem('identity',JSON.stringify(this.user));
                }
            },
                error => {
                console.log(<any>error)
            }
        );
    }


}