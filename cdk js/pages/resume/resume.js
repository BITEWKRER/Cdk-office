var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
        index: 0,
        sex: ['男', '女'],
        showModal: false,
        flag: '',
        content: {
            name: '',
            sex: '男',
            money: '',
            university: '',
            major: '',
            education: '',
            experiences: '',
            honers: '',
            phone: '',
            E_mail: '',
            purpose: '',
            communicate: '',
            self_evaluation: '',
        },
        name: '',
        university: '',
        major: '',
        education: '',
        phone: '',
        E_mail: '',
        purpose: '',
        money: '',
        length: 0,
        total1: 0,
        total2: 0,
        total3: 0,
        total4: 0,
        nothidden: true,
        info: '',
        tipType: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        if (app.globalData.content != '') {
            this.setData({
                content: app.globalData.content,
                name: app.globalData.content.name,
                university: app.globalData.content.university,
                major: app.globalData.content.major,
                education: app.globalData.content.education,
                phone: app.globalData.content.phone,
                E_mail: app.globalData.content.E_mail,
                purpose: app.globalData.content.purpose,
                money: app.globalData.content.money,
                total1: app.globalData.content.honers.length,
                total2: app.globalData.content.experiences.length,
                total3: app.globalData.content.communicate.length,
                total4: app.globalData.content.self_evaluation.length,
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
    bindPickerChange: function(e) {
        this.setData({
            index: e.detail.value,
        });
        this.setData({
            ['content.sex']: this.data.sex[this.data.index]
        })
    },
    touch: function(e) {
        switch (e.target.dataset.id) {
            case 'baseInfo':
                this.setData({
                    showModal: true,
                    flag: 'baseInfo',
                    nothidden: false
                })
                break;
            case 'education':
                this.setData({
                    showModal: true,
                    flag: 'education',
                    nothidden: false
                })
                break;
        }
    },
    cancel: function(e) {
        this.setData({
            showModal: false,
            flag: '',
            nothidden: true
        })
    },
    confirm: function(e) {
        switch (e.target.dataset.id) {
            case 'baseInfo':

                this.setData({
                    showModal: false,
                    nothidden: true,
                    ['content.name']: this.data.name,
                    ['content.sex']: this.data.sex[this.data.index],
                    ['content.phone']: this.data.phone,
                    ['content.purpose']: this.data.purpose,
                    ['content.E_mail']: this.data.E_mail,
                    ['content.money']: this.data.money,
                })
                break;
            case 'education':
                this.setData({
                    showModal: false,
                    nothidden: true,
                    ['content.university']: this.data.university,
                    ['content.education']: this.data.education,
                    ['content.major']: this.data.major,
                })
                break;
                // case 'experiences':
                //   this.setData({
                //     showModal: false,
                //     ['content.experiences']: e.detail.value,
                //     total2: e.detail.value.length
                //   });
                //   break;
                // case 'self_evaluation':
                //   this.setData({
                //     showModal: false,
                //     ['content.self_evaluation']: e.detail.value,
                //     total3: e.detail.value.length
                //   })
                //   break;
                // case 'honers':
                //   this.setData({
                //     showModal: false,
                //     ['content.honers']: e.detail.value,
                //     total1: e.detail.value.length
                //   })
                //   break;
        }
    },
    bindchange: function(e) {
        switch (e.target.dataset.id) {
            case 'name':
                this.setData({
                    name: e.detail.value
                })
                break;
            case 'money':
                this.setData({
                    money: e.detail.value
                })
                break;
            case 'university':
                this.setData({
                    university: e.detail.value
                })
                break;
            case 'major':
                this.setData({
                    major: e.detail.value
                })
                break;
            case 'education':
                this.setData({
                    education: e.detail.value
                })
                break;
            case 'phone':
                this.setData({
                    phone: e.detail.value
                })
                break;
            case 'E_mail':
                this.setData({
                    E_mail: e.detail.value
                })
                break;
            case 'purpose':
                this.setData({
                    purpose: e.detail.value
                })
                break;
        }
    },
    textConfirm: function(e) {
        if (e.target.dataset.id === 'experiences') {
            this.setData({
                ['content.experiences']: e.detail.value,
                total2: e.detail.value.length
            });
        } else if (e.target.dataset.id === 'self_evaluation') {
            this.setData({
                ['content.self_evaluation']: e.detail.value,
                total4: e.detail.value.length
            })
        } else if (e.target.dataset.id === 'communicate') {
            this.setData({
                ['content.communicate']: e.detail.value,
                total3: e.detail.value.length
            })
        } else {
            this.setData({
                ['content.honers']: e.detail.value,
                total1: e.detail.value.length
            })
        }
    },
    _export: function() {
        var that = this;
        
        wx.showActionSheet({
            itemList: ["模板一", "模板二"],
            success(res) {
                if (res.tapIndex === 0) {
                    that.chooseModel(0)
                } else if (res.tapIndex === 1) {
                    that.chooseModel(1)
                }  
            }
        })
    },
    chooseModel(flag){
        var that = this
        wx.request({
            url: 'https://renovation.12zw.club/profile',
            data: {
                flag: flag,
                openid: app.globalData.openid,
                content: that.data.content
            },
            success(res) {
                wx.showModal({
                    title: '保存成功',
                    content: '返回首页或查看文件(若出现显示空白的情况，请点击预览窗口的右上方，使用其他应用打开此文件，可能会出现拉伸情况，电脑显示则可以正常显示。)',
                    confirmText: '查看文件',
                    cancelText: '返回首页',
                    success: (res) => {
                        if (res.confirm) {
                            wx.downloadFile({
                                url: 'https://renovation.12zw.club/downloadWord?name=' + that.data.content.name + '&openid=' + app.globalData.openid,
                                success(res) {
                                    wx.openDocument({
                                        filePath: res.tempFilePath,
                                        fileType: 'docx',
                                        success(res){
                                            // console.log(res)
                                        },fail(res){
                                            // console.log(res)
                                        }
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
            fail(res) {
                wx.showToast({
                    title: '存储失败',
                    icon: 'none'
                })
            }
        })
    }

})