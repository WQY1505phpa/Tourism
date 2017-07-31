<?php

/******
 * User: 降省心
 * QQ:1348550820
 * Website: http://www.hanfu8.top
 * Date: 2017/6/6
 * Time: 16:01
 ******/
namespace frontend\controllers;

use frontend\models\IndexModel;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;


class ShowController extends Controller
{
    public $enableCsrfValidation = false;//关闭csrf


    //首页
    public function actionIndex()
    {
        $sum = (new \yii\db\Query())
            ->select(['count(*)'])
            ->from('barnd')
            ->count();
        //print_r($sum);die;
        $page = new Pagination([
            'defaultPageSize'=>2,
            'totalCount'=>$sum,
        ]);
        $reg = (new \yii\db\Query())->select(['*','count(com_id)'])
            ->from('barnd')
            ->leftJoin('barnd_comment','barnd.id=barnd_comment.b_id')
            ->leftJoin('barnd_com','barnd.id=barnd_com.com_id')
            ->groupBy('com_id')
            ->orderBy('id desc')
            ->limit($page->limit)
            ->offset($page->offset)
            ->all();
        return $this->render('index',['reg'=>$reg,'pages'=>$page]);
    }

    //笑话添加
    public function actionAdd()
    {
        return $this->render('add');
    }

    //笑话添加处理
    public function actionAdd_d()
    {
        $data = Yii::$app->request->post();
        $db = (Yii::$app->db->createCommand());
        $reg = $db->insert('barnd',$data)->execute();
        $id = (new \yii\db\Query())->select(['id'])->from('barnd')->orderBy(['id'=>SORT_DESC])->one();
        $db->insert('barnd_comment',['b_id'=>$id['id']])->execute();
        if($reg) return $this->redirect('?r=show/index');
    }

    //顶
    public function actionTop()
    {
        $b_id = Yii::$app->request->get("b_id");
        $db = Yii::$app->db->createCommand();
        $c_top = (new \yii\db\Query())->select(['c_top'])->from('barnd_comment')->where(['b_id'=>$b_id])->one();
        $c_top = $c_top['c_top']+1;
        $reg = $db->update('barnd_comment',['c_top'=>$c_top],"b_id=$b_id")->execute();
        if($reg) return $this->redirect('?r=show/index');
    }

    //踩
    public function actionLow()
    {
        $b_id = Yii::$app->request->get("b_id");
        $db = Yii::$app->db->createCommand();
        $c_top = (new \yii\db\Query())->select(['c_low'])->from('barnd_comment')->where(['b_id'=>$b_id])->one();
        $c_top = $c_top['c_low']+1;
        $reg = $db->update('barnd_comment',['c_low'=>$c_top],"b_id=$b_id")->execute();
        if($reg) return $this->redirect('?r=show/index');
    }

    //评论
    public function actionComment()
    {
        $b_id = Yii::$app->request->get("b_id");
        $db = Yii::$app->db->createCommand();
        $reg = (new \yii\db\Query())->select(['*'])->from('barnd_comment')->where(['b_id'=>$b_id])->leftJoin('barnd','barnd.id=barnd_comment.b_id')->one();
        $data = (new \yii\db\Query())->select(['*'])->from('barnd_com')->where(['com_id'=>$b_id])->all();
        return $this->render('common',['reg'=>$reg,'data'=>$data]);
    }

    //评论添加
    public function actionCom()
    {
        $data = Yii::$app->request->post();
        $db = (Yii::$app->db->createCommand());
        $reg = $db->insert('barnd_com',$data)->execute();
        if($reg) return $this->redirect('?r=show/index');
    }


















}