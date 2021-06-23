var dateTimePicker = require('../../utils/util.js');
var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
        showModal: false,
        showEnter: false,
        name: '',
        department: '',
        who: '',
        accountList: [],
        accountListLength: 0,
        delivery_date: '',
        merchandise_name: '',
        should_get: 0,
        already_get: 0,
        left_money: 0,
        arrival_date: '',
        responsible_for: '',
        remark: '',
        charNumber: 0,
        dateTimeArray1: null,
        dateTime1: null,
        dateTimeArray: null,
        dateTime: null,
        tipType: '',
        info: '',
        show: false
    },
    onShow() {
        this.setData({
            show: true
        })
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        if (app.globalData.content != '') {
            this.setData({
                accountListLength: app.globalData.content.length,
                accountList: app.globalData.content,
                name: app.globalData.name,
                department: app.globalData.department,
                who: app.globalData.who,
            })
        }
        app.globalData.content = "";
        app.globalData.name = '';

        var obj1 = dateTimePicker.dateTimePicker(this.data.startYear, this.data.endYear);
        // 精确到分的处理，将数组的秒去掉
        var lastArray = obj1.dateTimeArray.pop();
        var lastTime = obj1.dateTime.pop();

        this.setData({
            dateTimeArray1: obj1.dateTimeArray,
            dateTime1: obj1.dateTime,
            dateTimeArray: obj1.dateTimeArray,
            dateTime: obj1.dateTime,
        });
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {


    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function() {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function() {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function() {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function() {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    },
    add: function(e) {
        this.setData({
            showEnter: true,
        })
    },
    back: function() {
        wx.switchTab({
            url: '../../../../index/index'
        })
    },
    cancel: function(e) {
        this.setData({
            showModal: false,
            showEnter: false,
            name: '',
            department: '',
            who: '',
            delivery_date: '',
            merchandise_name: '',
            should_get: 0,
            already_get: 0,
            left_money: 0,
            arrival_date: '',
            remark: '',
            charNumber: 0
        })
    },
    confirm: function(e) {
        if (this.data.name === '' || this.data.department === '' || this.data.who === '') {
            this.setData({
                info: '姓名，部门，负责人不能为空！',
                tipType: 'error'
            })
            return
        } else {
            this.setData({
                showModal: false
            })
        }

        var that = this;
        if (that.data.accountListLength == 0) {
            that.setData({
                info: '无数据',
                tipType: 'error'
            })
            return
        }

        wx.request({
            url: 'https://renovation.12zw.club/selfBill',
            header: {
                'Content-Type': 'application/json'
            },
            data: {
                billFlag: 1,
                department: this.data.department,
                name: this.data.name,
                who: this.data.who,
                openid: app.globalData.openid,
                content: this.data.accountList
            },
            success: function(res) {
                if (res.statusCode == 200) {
                    wx.showModal({
                        title: '保存成功',
                        content: '返回首页或查看文件(若出现显示空白的情况，请点击预览窗口的右上方，使用其他应用打开此文件，可能会出现拉伸情况，电脑显示则可以正常显示。)',
                        confirmText: '查看文件',
                        cancelText: '返回首页',
                        success: (res) => {
                            if (res.confirm) {
                                wx.downloadFile({
                                    url: 'https://renovation.12zw.club/downloadWord?name=' + that.data.name + '&openid=' + app.globalData.openid,
                                    success(res) {
                                        wx.openDocument({
                                            filePath: res.tempFilePath,
                                            fileType: 'docx',
                                        })
                                    }
                                })
                            } else if (res.cancel) {
                                wx.switchTab({
                                    url: '../../pages/index/index',
                                })
                            }
                        }
                    })
                }
            },
            fail: function(res) {
                this.setData({
                    info: '保存失败，请检查网络！',
                    tipType: 'error'
                })
            }
        });
    },
    itemConfirm: function(e) {
        if (this.data.arrival_date === '') {
            this.setData({
                arrival_date: (dateTimePicker.formatTime1(new Date()) + ' ' + dateTimePicker.formatTime2(new Date())).substr(0, 16)
            })
        }
        if (this.data.delivery_date === '') {
            this.setData({
                delivery_date: (dateTimePicker.formatTime1(new Date()) + ' ' + dateTimePicker.formatTime2(new Date())).substr(0, 16)
            })
        }
        if (this.data.merchandise_name === '' || this.data.should_get === '' || this.data.already_get === '') {
            this.setData({
                info: '商品名称，应付金额，已付金额均不能为空',
                tipType: 'error'
            })
        } else if (this.data.responsible_for === '') {
            this.setData({
                info: '负责人不能为空！',
                tipType: 'error'
            })
        } else {
            var customID = this.data.accountList.length;

            var item = [
                'accountList[' + customID + '].merchandise_name',
                'accountList[' + customID + '].delivery_date',
                'accountList[' + customID + '].should_get',
                'accountList[' + customID + '].already_get',
                'accountList[' + customID + '].left_money',
                'accountList[' + customID + '].arrival_date',
                'accountList[' + customID + '].responsible_for',
                'accountList[' + customID + '].remark',
            ];

            this.setData({
                showEnter: false,
                [item[0]]: this.data.merchandise_name,
                [item[1]]: this.data.delivery_date,
                [item[2]]: parseFloat(this.data.should_get).toFixed(2),
                [item[3]]: parseFloat(this.data.already_get).toFixed(2),
                [item[4]]: parseFloat((this.data.should_get - this.data.already_get)).toFixed(2),
                [item[5]]: this.data.arrival_date,
                [item[6]]: this.data.responsible_for,
                [item[7]]: this.data.remark,
                delivery_date: '',
                merchandise_name: '',
                should_get: 0,
                already_get: 0,
                left_money: 0,
                arrival_date: '',
                remark: '',
                charNumber: 0
            });

            this.setData({
                accountListLength: this.data.accountList.length,
            })
        }
    },
    bindchange: function(e) {
        switch (e.target.dataset.id) {
            case 'name':
                this.setData({
                    name: e.detail.value
                })
                break;
            case 'department':
                this.setData({
                    department: e.detail.value
                })
                break;
            case 'who':
                this.setData({
                    who: e.detail.value
                })
                break;
            case 'merchandise_name':
                this.setData({
                    merchandise_name: e.detail.value
                })
                break;
            case 'should_get':
                this.setData({
                    should_get: e.detail.value
                })
                break;
            case 'already_get':
                this.setData({
                    already_get: e.detail.value
                })
                break;
            case 'responsible_for':
                this.setData({
                    responsible_for: e.detail.value
                })
                break;
            case 'remark':
                this.setData({
                    remark: e.detail.value,
                    charNumber: e.detail.value.length
                })
                break;
        }
    },

    changeDateTime1(e) {
        switch (e.target.dataset.id) {
            case 'delivery_date':
                this.setData({
                    dateTime: e.detail.value
                });
                break;
            case 'arrival_date':
                this.setData({
                    dateTime1: e.detail.value
                });
                break;
        }
    },
    changeDateTimeColumn1(e) {

        var arr = this.data.dateTime1,
            dateArr = this.data.dateTimeArray1;

        arr[e.detail.column] = e.detail.value;
        dateArr[2] = dateTimePicker.getMonthDay(dateArr[0][arr[0]], dateArr[1][arr[1]]);

        var time = this.data.dateTimeArray1[0][this.data.dateTime1[0]] + '-' +
            this.data.dateTimeArray1[1][this.data.dateTime1[1]] + '-' +
            this.data.dateTimeArray1[2][this.data.dateTime1[2]] + ' ' +
            this.data.dateTimeArray1[3][this.data.dateTime1[3]] + ":" +
            this.data.dateTimeArray1[4][this.data.dateTime1[4]];

        switch (e.target.dataset.id) {
            case 'delivery_date':
                this.setData({
                    dateTimeArray: dateArr,
                    dateTime: arr,
                    delivery_date: time
                })
                break;
            case 'arrival_date':
                this.setData({
                    dateTimeArray1: dateArr,
                    dateTime1: arr,
                    arrival_date: time
                })
                break;
        }
    },
    _export: function(e) {
        if(this.data.accountList !=[] && this.data.name !=[] && this.data.department!= [] && this.data.who !=[]){
            this.confirm();
        }else{
            this.setData({
                showModal: true
            })
        }

    },
    cut: function(e) {
        tmp = dateTimePicker.deleteObject(this.data.accountList, e.target.dataset.id)
        this.setData({
            accountList: tmp,
            accountListLength: tmp.length
        })
    },
})