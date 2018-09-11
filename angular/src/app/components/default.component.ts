
import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../services/user.service';
import { TaskService } from '../services/task.service';
import { Task } from '../models/task';


@Component({
    selector: 'default',
    templateUrl: '../views/default.html',
    providers:[UserService, TaskService]
})
export class DefaultComponent implements OnInit{
     public title: string;
     public identity;
     public token;
     public tasks: Array<Task>;

     constructor(
           private _route: ActivatedRoute,
           private _router: Router,
           private _userService: UserService,
           private _taskService: TaskService
     ){
         this.title ='HomePage';
         this.identity =this._userService.getIdentity();
         this.token =this._userService.getToken();
     }
    ngOnInit(){
        console.log('defa Compononant');
        this.getAllTasks();
    }
    getAllTasks(){
        this._route.params.forEach((params:Params) => {
           let page = +params['page'];
            if(!page){
                page = 1;
            }
             this._taskService.getTasks(this.token,page).subscribe(
                     response =>{
                     if(response.status == 'success'){
                         this.tasks =  response.data;
                     }
                 },
                     error => {
                     console.log(<any>error)
                 }
             )

        });
    }

}