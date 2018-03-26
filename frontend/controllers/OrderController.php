<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\validators\DateValidator;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\Order;
use frontend\models\Menu;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\grid\ActionColumn;
use frontend\models\Restaurants;
use frontend\models\Imagesmenu;
use yii\web\NotFoundHttpException;

class OrderController extends Controller {

    public function behaviors() {

        return [
                'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'index', 'upload', 'restaurant', 'delete', 'update', 'category', 'restaurant', 'view', 'create'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index', 'upload', 'restaurant', 'update', 'delete', 'category', 'restaurant', 'view', 'create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($date = null) {
        $dateValue = $date ? $date : date('Y-m-d');
        $validator = new DateValidator(['format' => 'php:Y-m-d']);
        if (!$validator->validate($dateValue)) {
            throw new BadRequestHttpException('Wrong date format');
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateValue);
        $tomorrow = $date->add(new \DateInterval('P1D'));
        $yesterday = $date->sub(new \DateInterval('P1D'));

        $sort = new Sort([
            'attributes' => [

                'restaurant' => [
                    'asc' => ['restaurantId' => SORT_ASC],
                    'desc' => ['restaurantId' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => 'Restauracji',
                ],
            ],
        ]);


        $model = Order::find()
                ->where(['>', 'data', $date->format('Y-m-d')])
                ->andWhere(['<', 'data', $tomorrow->format('Y-m-d')])
                ->orderBy($sort->orders)
                ->all();

        return $this->render('index', [
            'model' => $model,
            'sort' => $sort,
            'date' => $date,
            'minDate' => \DateTime::createFromFormat('Y-m-d', '2000-01-01'),
            'today' => new \DateTime('now'),
            'tomorrow' => $tomorrow,
            'yesterday' => $yesterday,
        ]);
    }

    public function actionUwagi($id) {

        $model = $this->findModel($id);

        
        $order = new Order();

        if (Yii::$app->request->post()) {
            $order->load(Yii::$app->request->post());
            $order->uwagi = strip_tags($order->uwagi);

            $order->userId = Yii::$app->user->identity->id;
            $order->foodId = $model->id;
            $order->restaurantId = $model->restaurantId;
            $order->status = 0;
            if ($order->save()) {
                return $this->redirect(['index']);

                
            }
        }
        return $this->render('uwagi', [
                    'model' => $model,
                    'order' => $order
        ]);
    }

    protected function findModel($id) {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete() {
    if (Yii::$app->request->post('id')) {
        $id= Yii::$app->request->post('id');
        $model = Order::findOne($id);
        $model->delete();
        return $this->redirect(['index']);
    }
    }

    public function actionEdit() {

    if (Yii::$app->request->post() && Yii::$app->request->post('name')==Yii::$app->user->identity->username) {
        $id= Yii::$app->request->post('id');
        $order = Order::findOne($id);
        $model = Menu::findOne($order->foodId);

    
     return $this->render('uwagi', [
                    'model' => $model,
                    'order' => $order
        ]);
    
    } 
    else if (Yii::$app->request->post('Order')) {
            $id= Yii::$app->request->post('Order')['orderId'];
            $order = Order::findOne($id);
            $order->load(Yii::$app->request->post());
            $orderUwagi = strip_tags($order->uwagi);
            $order->uwagi = $orderUwagi;
            $order->update();
            //$order->save();
            if ($order->save()) {
                return $this->redirect(['index']);

               
            }
        }
        else{
       
        return $this->redirect(['index']);
    }
      
       
    }

    public function actionRestaurant($id) {
        $date = date('Y-m-d');
        $resturant = new Restaurants;
        $restaurant = Restaurants::findOne($id);
        $imagesMenu = Imagesmenu::find()->where(['restaurantId' => $id])->all();


        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where(['restaurantId' => $id])->andWhere(['>', 'data', $date]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        return $this->render('takeOrder', ['restaurant' => $restaurant,
                    'imagesMenu' => $imagesMenu,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrderCompleted($id) {
        $date = date('Y-m-d');

        $model = Order::find()->where(['restaurantId'=>$id])->andWhere(['>', 'data', $date])->all();
        
        foreach($model as $status){
        $status->status = 1;
        $status->save();
        }
        
       

        return $this->redirect("index");
    }

}
