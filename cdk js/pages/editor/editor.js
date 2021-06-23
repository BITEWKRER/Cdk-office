var app = getApp();
Page({
    data: {
        formats: {},
        readOnly: false,
        placeholder: '在这里尽情创作吧！',
        editorHeight: 300,
        keyboardHeight: 0,
        isIOS: false,
        content: '',
        error: '',
        tipType: '',
        info: '',
        name: '',
    },
    readOnlyChange() {
        this.setData({
            readOnly: !this.data.readOnly
        })
    },
    onLoad() {
        if (app.globalData.content != '') {
            this.setData({
                content: app.globalData.content,
                name: app.globalData.name
            })
        }
        app.globalData.content = "";
        app.globalData.name = '';


        const platform = wx.getSystemInfoSync().platform
        const isIOS = platform === 'ios'
        this.setData({
            isIOS
        })
        const that = this
        this.updatePosition(0)
        let keyboardHeight = 0
        wx.onKeyboardHeightChange(res => {
            if (res.height === keyboardHeight) return
            const duration = res.height > 0 ? res.duration * 1000 : 0
            keyboardHeight = res.height
            setTimeout(() => {
                wx.pageScrollTo({
                    scrollTop: 0,
                    success() {
                        that.updatePosition(keyboardHeight)
                        that.editorCtx.scrollIntoView()
                    }
                })
            }, duration)

        })
    },
    updatePosition(keyboardHeight) {
        const toolbarHeight = 50
        const {
            windowHeight,
            platform
        } = wx.getSystemInfoSync()
        let editorHeight = keyboardHeight > 0 ? (windowHeight - keyboardHeight - toolbarHeight) : windowHeight
        this.setData({
            editorHeight,
            keyboardHeight
        })
    },
    calNavigationBarAndStatusBar() {
        const systemInfo = wx.getSystemInfoSync()
        const {
            statusBarHeight,
            platform
        } = systemInfo
        const isIOS = platform === 'ios'
        const navigationBarHeight = isIOS ? 44 : 48
        return statusBarHeight + navigationBarHeight
    },
    onEditorReady() {
        const that = this
        wx.createSelectorQuery().select('#editor').context(function(res) {
            that.editorCtx = res.context
            that.setContent(that.data.content)
        }).exec()


    },
    blur() {
        this.editorCtx.blur()
    },
    format(e) {
        let {
            name,
            value
        } = e.target.dataset
        if (!name) return
        // console.log('format', name, value)
        this.editorCtx.format(name, value)

    },
    undo() {
        this.editorCtx.undo();
    },
    redo() {
        this.editorCtx.redo()
    },
    onStatusChange(e) {
        const formats = e.detail
        this.setData({
            formats
        })
    },
    insertDivider() {
        this.editorCtx.insertDivider({
            success: function() {
                console.log('insert divider success')
            }
        })
    },
    getContent(e) {
        var that = this;
        if (that.data.name != '') {
            that.confirm()
        } else {
            that.setData({
                show: true
            })
        }

    },
    cancel() {
        this.setData({
            show: false
        })
    },
    confirm() {
        var that = this;

        this.editorCtx.getContents({
            success: function(res) {
                // console.log(res.html)
                that.setData({
                    content: res.html
                })
                wx.request({
                    url: 'https://renovation.12zw.club/custom',
                    data: {
                        name: that.data.name,
                        openid: app.globalData.openid,
                        content: res.html
                    },
                    success: function(res) {
                        that.setData({
                            show: false
                        })
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
                    },
                    fail: function(e) {
                        that.setData({
                            tipType: 'error',
                            info: '保存失败'
                        })
                    }
                })
            }
        })
    },
    getChange(e) {
        this.setData({
            name: e.detail.value
        })
    },
    clear() {
        this.editorCtx.clear({
            success: function(res) {
                // console.log("clear success")
            }
        })
    },
    setContent(html) {
        var that = this
        that.editorCtx.setContents({
            html: html,
            success: (res) => {},
            fail: (res) => {}
        })
    },
    removeFormat() {
        this.editorCtx.removeFormat()
    },
    insertDate() {
        const date = new Date()
        const formatDate = `${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`
        this.editorCtx.insertText({
            text: formatDate
        })
    },
    insertImage() {
        const that = this
        wx.chooseImage({
            count: 1,
            success: function(res) {
                that.editorCtx.insertImage({
                    src: res.tempFilePaths[0],
                    data: {
                        id: 'abcd',
                        role: 'god'
                    },
                    width: '100%',
                    success: function() {
                        that.setData({
                            tipType: 'info',
                            info: '如果出现无法输入的情况，长按选中图片往下滑动即可'
                        })
                    }
                })
            }
        })
    }
})