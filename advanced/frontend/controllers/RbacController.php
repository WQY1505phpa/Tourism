<?php


namespace frontend\controllers;

use frontend\models\Form;
use frontend\models\Rbac;
use yii\web\Controller;

class RbacController extends Controller {

    //首页
    public function actionIndex()
    {
        return $this->render("index");
    }

    //创建权限
    public function actionAdd_rule()
    {
        $model = new Form();
        return $this->render("add_rule",['model'=>$model]);
    }
    //权限添加处理
    public function actionRule_add()
    {
        $data = \Yii::$app->request->post("Form");
        $item = $data['username'];
        /*操作核心代码开始*/
        $auth = \Yii::$app->authManager;
        //权限名: action
        $createPost = $auth->createPermission($item);
        //添加描述
        $createPost->description = '创建了 ' . $item . ' 许可';
        $auth->add($createPost);
        /*操作核心代码开始*/

        echo "<script>alert('创建成功');location.href=rbac</script>";
    }

    //添加角色
    public function actionRoles()
    {
        $model = new Form();
        return $this->render("roles",['model'=>$model]);
    }

    //添加角色处理
    public function actionRoles_dd()
    {
        $data = \Yii::$app->request->post("Form");
        $item = $data['username'];

        /*操作核心代码开始*/
        $auth = \Yii::$app->authManager;
        //角色名
        $role = $auth->createRole($item);
        //操作描述
        $role->description = '创建了 ' . $item . ' 角色';
        $auth->add($role);
        /*操作核心代码结束*/

        echo "<script>alert('创建成功');location.href=rbac</script>";
    }

    //给角色分配许可
    public function actionAuth_item()
    {
        /*操作核心代码开始*/
        $model = new Form();
        $mod = new Rbac();
        $data = $mod->auth_item();
        /*操作核心代码结束*/

        return $this->render("auth_item",['model'=>$model,'data'=>$data]);
    }

    //给角色分配许可处理
    public function actionAuth_item_dd()
    {
        $data = \Yii::$app->request->post("Form");

        $role='';
        $auth='';
        $reg = array();
        foreach ($data['auth'] as $v)
        {
            $auth=$v;
            foreach ($data['role'] as $key=>$val)
            {
                $role=$val;
                $reg[$role][]=$auth;
            }
        }
        //循环入库,方法太喽.懒得改了,记一下入库代码就行
        foreach ($reg as $k=>$v)
        {
            foreach ($v as $val)
            {
                /*操作核心代码开始*/
                $auth = \Yii::$app->authManager;
                //角色名
                $parent = $auth->createRole($k);
                //权限名
                $child = $auth->createPermission($val);
                $auth->addChild($parent, $child);
                /*操作核心代码结束*/
            }
        }
        echo "<script>alert('给角色分配许可');location.href=rbac</script>";
    }

    //给用户分配角色
    public function actionChild()
    {
        //查询所有角色
        $role = (new \yii\db\Query())
            ->select(['name'])
            ->from("auth_item")
            ->where(['type'=>1])->all();

        $role_new = array();
        foreach ($role as $v)
        {
            $role_new[$v['name']]=$v['name'];
        }
        //查询所有用户
        $user = (new \yii\db\Query())
            ->select(['username','id'])
            ->from("user")
            ->all();
        $user_new = array();
        foreach ($user as $v)
        {
            $user_new[$v['id']]=$v['username'];
        }

        $model = new Form();
        return $this->render("child",['model'=>$model,'role'=>$role_new,'user'=>$user_new]);
    }

    //给用户分配角色处理
    public function actionChild_dd()
    {
        $data = \Yii::$app->request->post('Form');
        $role='';
        $reg = array();
        foreach ($data['role'] as $v)
        {
            foreach ($data['auth'] as $key=>$val)
            {
                $role=$val;
                $reg[$role][]=$v;
            }
        }

        foreach ($reg as $k=>$v)
        {
            foreach ($v as $val)
            {

                /*操作核心代码开始*/
                $auth = \Yii::$app->authManager;
                $reader = $auth->createRole($k);//$k是角色
                $auth->assign($reader, $val);//$val为用户id
                /*操作核心代码开始*/

            }
        }
        echo "<script>alert('用户分配角色成功');location.href=rbac</script>";

    }

//   //验证用户是否有权限
//    /* 操作核心代码开始,
//     * 下面验证方法加入需要验证的控制器里面,或者自己写一个控制器,然后,其它控制器来继承
//     * 当该控制器内的方法被执行的时候,会自动验证用户是否有权限操作
//     * */
//   public function beforeAction($action)
//   {
//        $action = \Yii::$app->controller->action->id;
//        $data=\Yii::$app->user->can($action);
//       if($data){
//           return true;
//        }else{
//           throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
//        }
//    }
    /*操作核心代码开始*/
}




