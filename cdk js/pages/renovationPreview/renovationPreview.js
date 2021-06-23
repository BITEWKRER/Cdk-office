const app = getApp()

Page({
    data: {
        sum: "0.00",
        List: null,
        tipType: '',
        info: ''
    },
    onLoad: function(options) {
        var that = this;
        var value = app.globalData.List;
        var countall = app.globalData.sum;
        that.setData({
            List: value,
            sum: countall,
        });
    },
    GenerateForm: function(e) {
        var that = this;
        wx.request({
            url: 'https://renovation.12zw.club/getInfo',
            data: {
                flag: 1,
                sum: app.globalData.sum,
                addressName: app.globalData.renovationName,
                content: JSON.stringify(app.globalData.List),
                openid: app.globalData.openid
            },
            method: 'GET',
            header: {
                'content-type': 'application/json'
            }, // 设置请求的 header
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
                                    url: 'https://renovation.12zw.club/downloadWord?name=' + app.globalData.renovationName + '&openid=' + app.globalData.openid,
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
                }else{
                    that.setData({
                        tipType: 'info',
                        info: '错误代码：'+res.statusCode
                    })
                }
            },
            fail(res) {
                that.setData({
                    tipType: 'error',
                    info: '生成失败，请检查网络！'
                })
            }

        })
    }
})
