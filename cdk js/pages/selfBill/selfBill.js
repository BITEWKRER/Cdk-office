const app = getApp();
const time = require("../../utils/util.js");

Page({

    /**
     * 页面的初始数据
     */
    data: {
        index: 0,
        showModal: false,
        itemName: "",
        income_or_expenditure: ['支出', '收入'],
        money: "",
        time: '',
        ymd: '',
        remark: "",
        billList: [],
        billListLength: 0,
        charNumber: 0,
        tipType: '',
        info: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        if (app.globalData.content != '') {
            this.setData({
                ymd: time.formatTime1(new Date()),
                billList: app.globalData.content,
                billListLength: app.globalData.content.length,
            })
        }
        app.globalData.content = "";
        app.globalData.name = '';
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
    cancel: function() {
        this.setData({
            showModal: false,
            itemName: '',
            index: 0,
            money: '',
            remark: '',
            time: ''
        });
    },
    confirm: function(e) {
        var customID = this.data.billList.length;
        if (this.data.time === '') {
            this.setData({
                ymd: time.formatTime1(new Date()),
                time: time.formatTime2(new Date())
            })
        } else {
            this.setData({
                ymd: time.formatTime1(new Date()),
                time: this.data.time
            })
        }
        if (this.data.itemName === '') {
            this.setData({
                tipType: 'error',
                info: '名称是必需的！'
            })
        } else if (this.data.money === '') {
            this.setData({
                tipType: 'error',
                info: '金额是必需的！'
            })
        } else {
            var arr = [
                'billList[' + customID + '].itemName',
                'billList[' + customID + '].income_or_expenditure',
                'billList[' + customID + '].money',
                'billList[' + customID + '].ymd',
                'billList[' + customID + '].time',
                'billList[' + customID + '].remark'
            ]
            this.setData({
                showModal: false,
                [arr[0]]: this.data.itemName,
                [arr[1]]: this.data.index,
                [arr[2]]: parseFloat(this.data.money).toFixed(2),
                [arr[3]]: this.data.ymd,
                [arr[4]]: this.data.time,
                [arr[5]]: this.data.remark,
                itemName: '',
                index: 0,
                money: '',
                remark: '',
                time: '',
                ymd: ''
            });
            // console.log(this.data.billList)
            this.setData({
                billListLength: this.data.billList.length,
            })
        }
    },
    add: function() {
        this.setData({
            showModal: true
        })
    },
    cut: function(e) {
        tmp = time.deleteObject(this.data.billList, e.target.dataset.id)
        this.setData({
            billList: tmp,
            billListLength: tmp.length
        })
    },
    getChange: function(e) {
        switch (e.target.dataset.id) {
            case 'itemName':
                this.setData({
                    itemName: e.detail.value
                });
                break;
            case 'money':
                this.setData({
                    money: e.detail.value
                });
                break;
            case 'remark':
                this.setData({
                    remark: e.detail.value,
                    charNumber: e.detail.value.length
                });
                break;
            default:
                break;
        }
    },
    bindPickerChange: function(e) {
        this.setData({
            index: e.detail.value,
        });
    },
    bindTimeChange: function(e) {
        this.setData({
            time: e.detail.value
        });
    },
    _addEvent: function(e) {
        this.setData({
            showModal: true
        })
    },
    _export: function() {
        var that = this;
        if (that.data.billList == []) {
            this.setData({
                tipType: 'error',
                info: '无数据，无法存储导出'
            })
            return
        }
        wx.request({
            url: 'https://renovation.12zw.club/selfBill',
            header: {
                'Content-Type': 'application/json'
            },
            data: {
                billFlag: 0,
                openid: app.globalData.openid,
                content: that.data.billList
            },
            success: function(res) {
                var myDate = new Date();
                var year = myDate.getFullYear();
                var mouth = myDate.getMonth();
                var name = year + '-' + (mouth + 1) + '月收入支出账单';
                wx.showModal({
                    title: '保存成功',
                    content: '返回首页或查看文件(若出现显示空白的情况，请点击预览窗口的右上方，使用其他应用打开此文件，可能会出现拉伸情况，电脑显示则可以正常显示。)',
                    confirmText: '查看文件',
                    cancelText: '返回首页',
                    success: (res) => {
                        if (res.confirm) {
                            wx.downloadFile({
                                url: 'https://renovation.12zw.club/downloadWord?name=' + name + '&openid=' + app.globalData.openid,
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
            },
            fail: function(res) {
                this.setData({
                    tipType: 'error',
                    info: '保存失败，请检查网络！'
                })
            }
        });
    }
})