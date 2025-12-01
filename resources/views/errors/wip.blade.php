<style>
    .not-found-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100vh;
    }
    .not-found-wrapper .content {
        text-align: center;
    }
    .not-found-wrapper h2 {
        font-size: 130px;
        font-family: 'Rubik', sans-serif;
        color: #A00005;
        font-weight: bold;
        margin: 0px 0px 10px;
    }
    .not-found-wrapper p {
        font-size: 24px;
        font-family: 'Rubik', sans-serif;
        font-weight: bold;
        margin: 0px;
    }
    .not-found-wrapper button  {
        /* display: inline-block; */
        font-size: 13px;
        font-family: 'Rubik', sans-serif;
        color: #ffffff;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        background-color: #000000;
        border-radius: 20px;
        border: none;
        box-shadow: none;
        outline: none;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 10px 30px;
        margin-top: 40px;
    }
    .not-found-wrapper button :hover {
        background-color: #A00005;
        transition: all 0.3s ease;
    }
</style>
<head>
    <title>KGA | {{$module}} -  WORK IN PROGRESS</title>
</head>
<div class="not-found-wrapper">
    <div class="content">
        <h2>Sorry!</h2>
        <p>This Feature <strong>{{$module}}</strong> Currently Is Not Available !!!</p>
        <button onclick="location.href='{{route('home')}}'">Back to home</button >
    </div>
</div>