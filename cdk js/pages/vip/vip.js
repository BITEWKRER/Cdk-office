// pages/vip/vip.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        what:true,
        showModal:false,
        order:''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {

    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function () {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function () {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function () {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function () {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function () {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function () {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function () {

    },
    buy(e){
        if(e.target.dataset.id == 'zfb'){
            this.setData({
                what: true,
                showModal: true
            })
        }else{
            this.setData({
                what: false,
                showModal: true
            })
        }

    },
    cancel(){
        this.setData({
            showModal: false
        })
    },
    bindChange(e){
        this.setData({
            info:e.detail.value
        })
    },
    confirm(e){
        if (e.target.dataset.id == 'zfb') {
            if (this.data.info.length == 28){
                wx.showToast({
                    title: '购买成功',
                    icon: 'none'
                })
                this.setData({
                    what: false,
                    showModal: false
                })
            }else{
                wx.showToast({
                    title: '请输入正确的转账单号',
                    icon:'none'
                })
            }
        } else {
            if (this.data.info.length == 30) {
                wx.showToast({
                    title: '购买成功',
                    icon: 'none'
                })
                this.setData({
                    what: false,
                    showModal: false
                })
            } else {
                wx.showToast({
                    title: '请输入正确的转账单号',
                    icon: 'none'
                })
            }
        }
    }
})